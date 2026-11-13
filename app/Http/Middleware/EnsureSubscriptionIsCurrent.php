<?php

namespace App\Http\Middleware;

use App\Services\Subscriptions\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Expire les abonnements échus avant de traiter une requête candidat.
 */
class EnsureSubscriptionIsCurrent
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'candidate') {
            $this->subscriptionService->expireDueSubscriptions($user);
        }

        return $next($request);
    }
}
