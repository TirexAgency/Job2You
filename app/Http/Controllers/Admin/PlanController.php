<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * Display a listing of the subscription plans.
     */
    public function index(): View
    {
        $plans = Plan::query()
            ->withCount([
                'subscriptions',
                'subscriptions as active_subscriptions_count' => fn ($query) => $query->where('status', 'active'),
                'payments',
            ])
            ->orderBy('price')
            ->paginate(10);

        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request): RedirectResponse
    {
        $plan = Plan::create($this->validatedData($request));

        $this->activityLogger->adminAction(
            'Création plan',
            "« {$plan->name} » ({$plan->price} Ar, {$plan->sms_quota} SMS)",
        );

        return redirect()
            ->route('admin.plans.index')
            ->with('success', "Plan {$plan->name} créé avec succès.");
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $plan->update($this->validatedData($request, $plan));

        $this->activityLogger->adminAction(
            'Modification plan',
            "« {$plan->name} » ({$plan->price} Ar, {$plan->sms_quota} SMS)",
        );

        return redirect()
            ->route('admin.plans.index')
            ->with('success', "Plan {$plan->name} mis à jour avec succès.");
    }

    /**
     * Remove the specified plan when it has no subscription or payment history.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->exists() || $plan->payments()->exists()) {
            return redirect()
                ->back()
                ->with('error', "Le plan {$plan->name} est utilisé par des abonnements ou des paiements et ne peut pas être supprimé.");
        }

        $name = $plan->name;
        $plan->delete();

        $this->activityLogger->adminAction('Suppression plan', "« {$name} »");

        return redirect()
            ->route('admin.plans.index')
            ->with('success', "Plan {$name} supprimé avec succès.");
    }

    /**
     * Toggle the active status of the plan.
     */
    public function toggle(Plan $plan): RedirectResponse
    {
        $plan->update(['active' => ! $plan->active]);

        $this->activityLogger->adminAction(
            $plan->active ? 'Activation plan' : 'Désactivation plan',
            "« {$plan->name} »",
        );

        return redirect()
            ->back()
            ->with('success', "Statut du plan {$plan->name} mis à jour.");
    }

    /**
     * Validate plan attributes, including price and quotas.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Plan $plan = null): array
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('plans', 'name')->ignore($plan?->id),
            ],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'duration_days' => ['required', 'integer', 'min:0', 'max:3650'],
            'sms_quota' => ['required', 'integer', 'min:0', 'max:100000'],
            'cv_parsing_enabled' => ['boolean'],
            'active' => ['boolean'],
        ]);

        $validated['cv_parsing_enabled'] = $request->boolean('cv_parsing_enabled');
        $validated['active'] = $request->boolean('active');

        return $validated;
    }
}
