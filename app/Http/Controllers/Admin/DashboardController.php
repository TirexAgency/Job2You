<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobMatch;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'newUsers' => User::where('created_at', '>=', now()->subMonth())->count(),
            'totalOffers' => Offer::count(),
            'newOffers' => Offer::where('created_at', '>=', now()->subWeek())->count(),
            'totalMatches' => JobMatch::count(),
            'plans' => Plan::all(),
            'subscriptionsCount' => Subscription::where('status', 'active')->count(),
        ]);
    }
}
