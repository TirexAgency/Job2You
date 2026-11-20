<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['activeSubscriptions.plan'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => User::roleLabels(),
            'statuses' => User::statusLabels(),
        ]);
    }

    /**
     * Affiche le détail d'un utilisateur.
     */
    public function show(User $user): View
    {
        $user->load([
            'activeSubscriptions.plan',
            'subscriptions' => fn ($query) => $query->with('plan')->latest()->limit(5),
            'payments' => fn ($query) => $query->with('plan')->latest()->limit(5),
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Affiche le formulaire de modification d'un utilisateur.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => User::roleLabels(),
            'statuses' => User::statusLabels(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['candidate', 'admin', 'recruiter'])],
            'status' => ['required', Rule::in(['pending', 'active', 'inactive', 'suspended'])],
            'plan' => ['required', 'string', 'max:20'],
            'sms_quota' => ['required', 'integer', 'min:0'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'plan' => $validated['plan'],
            'sms_quota' => $validated['sms_quota'],
            'sms_sent' => 0,
            'email_verified_at' => now(),
        ]);

        $this->activityLogger->adminAction('Création utilisateur', "« {$user->name} » ({$user->email})");

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} créé avec succès.");
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:15', Rule::unique('users', 'phone')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['candidate', 'admin', 'recruiter'])],
            'status' => ['required', Rule::in(['pending', 'active', 'inactive', 'suspended'])],
            'plan' => ['required', 'string', 'max:20'],
            'sms_quota' => ['required', 'integer', 'min:0'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'status' => $validated['status'],
            'plan' => $validated['plan'],
            'sms_quota' => $validated['sms_quota'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $this->activityLogger->adminAction('Modification utilisateur', "« {$user->name} » ({$user->email})");

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} mis à jour avec succès.");
    }

    /**
     * Change rapidement le statut d'un compte.
     */
    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Vous ne pouvez pas modifier le statut de votre propre compte.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(User::statusLabels()))],
        ]);

        $previousStatus = $user->status;
        $user->update(['status' => $validated['status']]);

        $this->activityLogger->adminAction(
            'Changement statut utilisateur',
            "« {$user->name} » : {$previousStatus} → {$validated['status']}",
        );

        return back()->with('success', "Statut de {$user->name} mis à jour.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $userName = $user->name;
        $user->delete();

        $this->activityLogger->adminAction('Suppression utilisateur', "« {$userName} »");

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Utilisateur {$userName} supprimé avec succès.");
    }
}
