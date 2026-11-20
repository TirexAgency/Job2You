<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobMatch;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\Source;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        // Statistiques principales
        $stats = [
            'users' => [
                'total' => User::count(),
                'new' => User::where('created_at', '>=', now()->subMonth())->count(),
                'candidates' => User::where('role', 'candidate')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'pending' => User::where('status', 'pending')->count(),
            ],
            'offers' => [
                'total' => Offer::count(),
                'new' => Offer::where('created_at', '>=', now()->subWeek())->count(),
                'active' => Offer::where('status', 'active')->count(),
                'expired' => Offer::where('status', 'expired')->count(),
                'archived' => Offer::where('status', 'archived')->count(),
                'by_source' => Source::withCount(['offers' => fn ($q) => $q->where('status', 'active')])
                    ->orderByDesc('offers_count')
                    ->limit(5)
                    ->get(),
            ],
            'matches' => [
                'total' => JobMatch::count(),
                'above_threshold' => JobMatch::where('score', '>=', (float) Setting::get('matching.alert_threshold', 0.7))->count(),
                'recent' => JobMatch::where('created_at', '>=', now()->subDay())->count(),
            ],
            'subscriptions' => [
                'active' => Subscription::where('status', 'active')->count(),
                'by_plan' => Plan::withCount(['subscriptions' => fn ($q) => $q->where('status', 'active')])
                    ->orderByDesc('subscriptions_count')
                    ->get(),
                'revenue_month' => Payment::where('status', 'paid')
                    ->where('created_at', '>=', now()->subMonth())
                    ->sum('amount'),
            ],
            'payments' => [
                'paid' => Payment::where('status', Payment::STATUS_PAID)->count(),
                'pending' => Payment::where('status', Payment::STATUS_PENDING)->count(),
                'failed' => Payment::where('status', Payment::STATUS_FAILED)->count(),
                'refunded' => Payment::where('status', Payment::STATUS_REFUNDED)->count(),
                'revenue_total' => Payment::where('status', Payment::STATUS_PAID)->sum('amount'),
            ],
            'sms' => [
                'sent_month' => SmsLog::where('status', SmsLog::STATUS_SENT)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
                'failed_month' => SmsLog::where('status', SmsLog::STATUS_FAILED)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
            ],
            'sources' => [
                'total' => Source::count(),
                'active' => Source::where('active', true)->count(),
            ],
        ];

        $charts = $this->buildCharts($stats);

        // Activité récente
        $recentUsers = User::latest()->limit(5)->get(['id', 'name', 'email', 'role', 'status', 'created_at']);
        $recentOffers = Offer::with('source')->latest()->limit(5)->get(['id', 'title', 'company', 'source_id', 'status', 'created_at']);
        $recentPayments = Payment::with('user')->where('status', 'paid')->latest()->limit(5)->get(['id', 'amount', 'user_id', 'created_at']);

        return view('admin.dashboard', [
            'stats' => $stats,
            'charts' => $charts,
            'recentUsers' => $recentUsers,
            'recentOffers' => $recentOffers,
            'recentPayments' => $recentPayments,
            'refreshedAt' => now(),
        ]);
    }

    /**
     * Construit les séries utilisées par les graphiques du tableau de bord.
     *
     * @param  array<string, mixed>  $stats
     * @return array<string, mixed>
     */
    private function buildCharts(array $stats): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset));
        $periodStart = $months->first()->copy()->startOfMonth();

        $paidPayments = Payment::query()
            ->where('status', Payment::STATUS_PAID)
            ->where('created_at', '>=', $periodStart)
            ->get(['amount', 'created_at']);

        $users = User::query()
            ->where('created_at', '>=', $periodStart)
            ->get(['created_at']);

        $offers = Offer::query()
            ->where('created_at', '>=', $periodStart)
            ->get(['created_at']);

        return [
            'revenue' => $this->monthlySeries($months, $paidPayments, fn (Collection $items) => (float) $items->sum('amount')),
            'users' => $this->monthlySeries($months, $users),
            'offers' => $this->monthlySeries($months, $offers),
            'payment_status' => [
                ['key' => 'paid', 'label' => 'Payés', 'value' => $stats['payments']['paid']],
                ['key' => 'pending', 'label' => 'En attente', 'value' => $stats['payments']['pending']],
                ['key' => 'failed', 'label' => 'Échoués', 'value' => $stats['payments']['failed']],
                ['key' => 'refunded', 'label' => 'Remboursés', 'value' => $stats['payments']['refunded']],
            ],
            'offer_status' => [
                ['key' => 'active', 'label' => 'Actives', 'value' => $stats['offers']['active']],
                ['key' => 'expired', 'label' => 'Expirées', 'value' => $stats['offers']['expired']],
                ['key' => 'archived', 'label' => 'Archivées', 'value' => $stats['offers']['archived']],
            ],
        ];
    }

    /**
     * Agrège une collection par mois.
     *
     * @param  Collection<int, Carbon>  $months
     * @param  Collection<int, mixed>  $items
     * @param  (callable(Collection<int, mixed>): float)|null  $aggregate
     * @return array<int, array{label: string, value: float}>
     */
    private function monthlySeries(Collection $months, Collection $items, ?callable $aggregate = null): array
    {
        return $months->map(function ($month) use ($items, $aggregate): array {
            $monthItems = $items->filter(fn ($item): bool => $item->created_at?->isSameMonth($month) ?? false);
            $value = $aggregate
                ? $aggregate($monthItems)
                : (float) $monthItems->count();

            return [
                'label' => $month->format('m/Y'),
                'value' => $value,
            ];
        })->all();
    }
}
