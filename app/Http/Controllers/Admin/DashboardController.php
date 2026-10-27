<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobMatch;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Source;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
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
                'by_source' => Source::withCount(['offers' => fn ($q) => $q->where('status', 'active')])
                    ->orderByDesc('offers_count')
                    ->limit(5)
                    ->get(),
            ],
            'matches' => [
                'total' => JobMatch::count(),
                'above_threshold' => JobMatch::where('score', '>=', config('settings.matching.alert_threshold', 0.7))->count(),
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
            'sources' => [
                'total' => Source::count(),
                'active' => Source::where('active', true)->count(),
            ],
        ];

        // Activité récente
        $recentUsers = User::latest()->limit(5)->get(['id', 'name', 'email', 'role', 'status', 'created_at']);
        $recentOffers = Offer::with('source')->latest()->limit(5)->get(['id', 'title', 'company', 'source_id', 'status', 'created_at']);
        $recentPayments = Payment::with('user')->where('status', 'paid')->latest()->limit(5)->get(['id', 'amount', 'user_id', 'created_at']);

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentOffers', 'recentPayments'));
    }
}
