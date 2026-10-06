<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\CandidateSkillController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use App\Models\CvParse;
use App\Models\JobMatch;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\SmsLog;
use App\Models\Source;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $recentOffers = Offer::with(['source', 'offerSkills.skill'])
        ->active()
        ->recent()
        ->limit(3)
        ->get();

    return view('home', compact('recentOffers'));
})->name('home');

// Routes d'inscription avec rate limiting et middleware guest
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');
});

// Routes d'authentification Breeze (login, password reset)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1');

    // Routes mot de passe oublié
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Déconnexion (nécessite d'être authentifié)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// Routes de vérification email avec rate limiting
// verification.notice et verification.send sont accessibles sans auth (pour les utilisateurs déconnectés)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

// verification.verify nécessite auth (le lien contient un hash lié à l'utilisateur connecté)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

// Routes protégées (auth + verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        $smsQuota = $user->getSmsQuota();

        return view('dashboard', [
            'user' => $user,
            'smsQuota' => $smsQuota,
            'smsRemaining' => max($smsQuota - $user->sms_sent, 0),
        ]);
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/jobs', function () {
        return view('home');
    })->name('jobs.index');
});

// Routes de confirmation de mot de passe et mise à jour
Route::middleware('auth')->group(function () {
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('/password', [PasswordController::class, 'update'])
        ->name('password.update');
});

// Routes d'administration (admin uniquement)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // CRUD Utilisateurs (resource sans create/show/edit - utilisés dans des modals)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    Route::get('/offers', fn () => view('admin.offers.index', ['offers' => Offer::latest()->paginate(10)]))->name('offers.index');
    Route::get('/sources', fn () => view('admin.sources.index', ['sources' => Source::latest()->paginate(10)]))->name('sources.index');
    Route::get('/plans', fn () => view('admin.plans.index', ['plans' => Plan::all()]))->name('plans.index');
    Route::get('/subscriptions', fn () => view('admin.subscriptions.index', ['subscriptions' => Subscription::with(['user', 'plan'])->latest()->paginate(10)]))->name('subscriptions.index');
    Route::get('/payments', fn () => view('admin.payments.index', ['payments' => Payment::with(['user'])->latest()->paginate(10)]))->name('payments.index');
    Route::get('/sms', fn () => view('admin.sms.index', ['logs' => SmsLog::with(['user'])->latest()->paginate(10)]))->name('sms.index');
    Route::get('/logs', fn () => view('admin.logs.index'))->name('logs.index');
    Route::patch('/sources/{source}/toggle', function (Source $source) {
        $source->update(['active' => ! $source->active]);

        return redirect()->back();
    })->name('sources.toggle');
    Route::patch('/offers/{offer}/toggle', function (Offer $offer) {
        $offer->update(['status' => $offer->status === 'active' ? 'archived' : 'active']);

        return redirect()->back();
    })->name('offers.toggle');
    Route::patch('/plans/{plan}/toggle', function (Plan $plan) {
        $plan->update(['active' => ! $plan->active]);

        return redirect()->back();
    })->name('plans.toggle');
});

Route::middleware(['auth', 'role:candidate'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $profile = $request->user()->candidateProfile()->with(['experiences', 'educations', 'candidateSkills.skill'])->first();

        return view('candidate.dashboard', [
            'profile' => $profile,
            'view' => $request->query('view', 'resume'),
        ]);
    })->name('dashboard');

    Route::get('/profile', [CandidateProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CandidateProfileController::class, 'update'])->name('profile.update');

    Route::post('/experiences', [ExperienceController::class, 'store'])->name('experiences.store');
    Route::put('/experiences/{experience}', [ExperienceController::class, 'update'])->name('experiences.update');
    Route::delete('/experiences/{experience}', [ExperienceController::class, 'destroy'])->name('experiences.destroy');

    Route::post('/educations', [EducationController::class, 'store'])->name('educations.store');
    Route::put('/educations/{education}', [EducationController::class, 'update'])->name('educations.update');
    Route::delete('/educations/{education}', [EducationController::class, 'destroy'])->name('educations.destroy');

    Route::get('/cv', function (Request $request) {
        $subscription = $request->user()->activeSubscriptions()->with('plan')->first();
        $cvParsingEnabled = $subscription?->plan?->cv_parsing_enabled ?? false;
        $parses = $request->user()->cvParses()->latest()->get();

        return view('candidate.cv', compact('parses', 'cvParsingEnabled'));
    })->name('cv');
    Route::post('/cv', function (Request $request) {
        $subscription = $request->user()->activeSubscriptions()->with('plan')->first();
        abort_unless($subscription?->plan?->cv_parsing_enabled, 403, 'Votre plan n\'inclut pas l\'analyse de CV.');

        $validated = $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $path = $request->file('cv')->store('cvs', 'local');
        CvParse::create(['user_id' => $request->user()->id, 'file_path' => $path, 'status' => 'pending']);

        return redirect()->route('candidate.cv')->with('status', 'cv-uploaded');
    })->name('cv.upload');
    Route::post('/cv/{parse}/validate', function (Request $request, CvParse $parse) {
        abort_unless($parse->user_id === $request->user()->id, 403);
        $parse->update(['validated_at' => now(), 'status' => 'done']);

        return redirect()->route('candidate.cv')->with('status', 'cv-validated');
    })->name('cv.validate');

    Route::post('/checkout/{plan}', function (Request $request, Plan $plan) {
        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'sandbox-'.str()->uuid(),
            'amount' => $plan->price,
            'status' => 'pending',
            'payload_reference' => str()->uuid(),
        ]);

        return redirect()->route('candidate.payment.sandbox', $payment);
    })->name('checkout');
    Route::get('/payment/{payment}/sandbox', function (Request $request, Payment $payment) {
        abort_unless($payment->user_id === $request->user()->id, 403);

        return view('candidate.payment-sandbox', compact('payment'));
    })->name('payment.sandbox');
    Route::post('/payment/{payment}/sandbox/{outcome}', function (Request $request, Payment $payment, string $outcome) {
        abort_unless($payment->user_id === $request->user()->id, 403);
        abort_unless(in_array($outcome, ['success', 'failed'], true), 404);

        $payment->update(['status' => $outcome === 'success' ? 'paid' : 'failed']);

        if ($outcome === 'success') {
            $plan = $payment->plan;
            $user = $request->user();
            $user->activeSubscriptions()->update(['status' => 'inactive', 'ends_at' => now()]);
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->duration_days ?: 30),
                'sms_remaining' => $plan->sms_quota,
                'status' => 'active',
            ]);
            $user->update(['plan' => strtolower($plan->name), 'sms_quota' => $plan->sms_quota, 'sms_sent' => 0]);
        }

        return redirect()->route('candidate.subscription')->with('status', 'plan-changed');
    })->name('payment.sandbox.confirm');

    Route::get('/recommendations', function (Request $request) {
        $matches = JobMatch::with(['offer.source'])->where('user_id', $request->user()->id)->orderByDesc('score')->get();

        return view('candidate.recommendations', compact('matches'));
    })->name('recommendations');
    Route::get('/sms', function (Request $request) {
        $logs = SmsLog::where('user_id', $request->user()->id)->latest()->get();
        $alertsEnabled = $request->user()->candidateProfile?->alerts_enabled ?? false;

        return view('candidate.sms', compact('logs', 'alertsEnabled'));
    })->name('sms');
    Route::post('/sms/alerts', function (Request $request) {
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);
        $profile->update(['alerts_enabled' => $request->boolean('alerts_enabled')]);

        return redirect()->route('candidate.sms')->with('status', 'alerts-updated');
    })->name('sms.alerts');
    Route::delete('/sms/{log}', function (Request $request, SmsLog $log) {
        abort_unless($log->user_id === $request->user()->id, 403);
        $log->delete();

        return redirect()->route('candidate.sms')->with('status', 'sms-deleted');
    })->name('sms.destroy');
    Route::get('/subscription', function (Request $request) {
        $subscription = $request->user()->activeSubscriptions()->with('plan')->first();
        $plans = Plan::where('active', true)->orderBy('price')->get();

        return view('candidate.subscription', compact('subscription', 'plans'));
    })->name('subscription');
    Route::post('/subscription/change', function (Request $request) {
        $validated = $request->validate(['plan_id' => ['required', 'exists:plans,id']]);
        $plan = Plan::findOrFail($validated['plan_id']);
        $user = $request->user();

        // Clôture l'abonnement actif actuel
        $user->activeSubscriptions()->update(['status' => 'inactive', 'ends_at' => now()]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_days),
            'sms_remaining' => $plan->sms_quota,
            'status' => 'active',
        ]);

        $user->update([
            'plan' => strtolower($plan->name),
            'sms_quota' => $plan->sms_quota,
            'sms_sent' => 0,
        ]);

        return redirect()->route('candidate.subscription')->with('status', 'plan-changed');
    })->name('subscription.change');

    Route::post('/skills', [CandidateSkillController::class, 'store'])->name('skills.store');
    Route::delete('/skills/{candidateSkill:skill_id}', [CandidateSkillController::class, 'destroy'])->name('skills.destroy');
});

// Routes publiques
Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/how-it-works', function () {
    return view('how-it-works');
})->name('how-it-works');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// Routes offres (publiques)
Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
Route::get('/offers/{offer}', [OfferController::class, 'show'])->name('offers.show');
