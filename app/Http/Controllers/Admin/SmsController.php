<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Historique des SMS et suivi des quotas.
 */
class SmsController extends Controller
{
    /**
     * Liste les SMS envoyés avec filtres et indicateurs de quota.
     */
    public function index(Request $request): View
    {
        $logs = SmsLog::query()
            ->with(['user', 'subscription.plan'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('quota_source'), fn ($query) => $query->where('quota_source', $request->string('quota_source')->toString()))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('recipient', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhere('provider_reference', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $monthStart = now()->startOfMonth();

        $stats = [
            'sent_month' => SmsLog::query()->where('status', SmsLog::STATUS_SENT)->where('created_at', '>=', $monthStart)->count(),
            'failed_month' => SmsLog::query()->where('status', SmsLog::STATUS_FAILED)->where('created_at', '>=', $monthStart)->count(),
            'pending' => SmsLog::query()->where('status', SmsLog::STATUS_PENDING)->count(),
            'quota_consumed' => SmsLog::query()->where('quota_consumed', true)->count(),
            'active_subscriptions' => Subscription::query()
                ->where('status', 'active')
                ->where(function ($query): void {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->count(),
            'remaining_quota' => (int) Subscription::query()
                ->where('status', 'active')
                ->where(function ($query): void {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->sum('sms_remaining'),
            'sent_by_source' => SmsLog::query()
                ->select('quota_source', DB::raw('COUNT(*) as total'))
                ->whereNotNull('quota_source')
                ->groupBy('quota_source')
                ->pluck('total', 'quota_source'),
        ];

        return view('admin.sms.index', [
            'logs' => $logs,
            'stats' => $stats,
            'statuses' => [
                SmsLog::STATUS_PENDING => 'En attente',
                SmsLog::STATUS_SENT => 'Envoyé',
                SmsLog::STATUS_FAILED => 'Échoué',
            ],
        ]);
    }
}
