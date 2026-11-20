<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OfferController as AdminOfferController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SmsController as AdminSmsController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateDashboardController;
use App\Http\Controllers\CandidatePaymentController;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\CandidateSkillController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Webhooks\FiveOnePayWebhookController;
use App\Models\CvParse;
use App\Models\JobMatch;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\SmsLog;
use App\Models\Subscription;
use App\Services\MatchingService;
use App\Services\Sms\SmsQuotaService;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $recentOffers = Offer::with(['source', 'offerSkills.skill'])
        ->active()
        ->recent()
        ->limit(3)
        ->get();

    $activeOffersCount = Offer::active()->count();

    return view('home', compact('recentOffers', 'activeOffersCount'));
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
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');

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

    // CRUD Utilisateurs (création et modification également disponibles en modals)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');

    // Paramètres de matching (pondérations et seuil d'alerte)
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // CRUD Sources (resource sans create/show/edit - utilisés dans des modals)
    Route::resource('sources', SourceController::class)->except(['create', 'show', 'edit']);
    Route::patch('/sources/{source}/toggle', [SourceController::class, 'toggle'])->name('sources.toggle');
    Route::post('/sources/{source}/scrape', [SourceController::class, 'scrape'])->name('sources.scrape');

    // Journal d'activité (logs métier lisibles)
    Route::get('/activity-log', ActivityLogController::class)->name('activity-log.index');

    // Modération et suppression des offres
    Route::get('/offers', [AdminOfferController::class, 'index'])->name('offers.index');
    Route::patch('/offers/{offer}/status', [AdminOfferController::class, 'updateStatus'])->name('offers.status');
    Route::delete('/offers/{offer}', [AdminOfferController::class, 'destroy'])->name('offers.destroy');
    // CRUD Plans (resource sans create/show/edit - utilisés dans des modals)
    Route::resource('plans', PlanController::class)->except(['create', 'show', 'edit']);
    Route::patch('/plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('plans.toggle');
    Route::get('/subscriptions', fn () => view('admin.subscriptions.index', ['subscriptions' => Subscription::with(['user', 'plan'])->latest()->paginate(10)]))->name('subscriptions.index');
    // Suivi et remboursement des paiements
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::patch('/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');
    // Historique SMS et quotas
    Route::get('/sms', [AdminSmsController::class, 'index'])->name('sms.index');
    Route::get('/logs', [ActivityLogController::class, 'rawLogs'])->name('logs.index');
});

Route::middleware(['auth', 'role:candidate', 'subscription.current'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/dashboard', CandidateDashboardController::class)->name('dashboard');

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
        $validated = $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $path = $request->file('cv')->store('cvs', 'local');
        CvParse::create(['user_id' => $request->user()->id, 'file_path' => $path, 'status' => 'pending']);

        return redirect()->route('candidate.cv')->with('status', 'cv-uploaded');
    })->middleware('plan.feature:cv_parsing')->name('cv.upload');
    Route::post('/cv/{parse}/validate', function (Request $request, CvParse $parse) {
        abort_unless($parse->user_id === $request->user()->id, 403);
        $parse->update(['validated_at' => now(), 'status' => 'done']);

        return redirect()->route('candidate.cv')->with('status', 'cv-validated');
    })->middleware('plan.feature:cv_parsing')->name('cv.validate');

    Route::post('/checkout/{plan}', [CandidatePaymentController::class, 'checkout'])->name('checkout');
    Route::get('/payment/{payment}/return', [CandidatePaymentController::class, 'returnFromProvider'])->name('payment.return');
    Route::get('/payment/{payment}/status', [CandidatePaymentController::class, 'status'])->name('payment.status');
    Route::get('/payment/{payment}/sandbox', [CandidatePaymentController::class, 'sandbox'])->name('payment.sandbox');
    Route::post('/payment/{payment}/sandbox/{outcome}', [CandidatePaymentController::class, 'confirmSandbox'])->name('payment.sandbox.confirm');

    Route::get('/recommendations', function (Request $request) {
        $user = $request->user();
        $profile = $user->candidateProfile;

        if ($profile && JobMatch::where('user_id', $user->id)->doesntExist()) {
            app(MatchingService::class)->computeForProfile($profile);
        }

        $matches = JobMatch::with(['offer.source', 'offer.offerSkills.skill'])
            ->where('user_id', $user->id)
            ->orderByDesc('score')
            ->get();

        return view('candidate.recommendations', compact('matches', 'profile'));
    })->name('recommendations');
    Route::get('/sms', function (Request $request, SmsQuotaService $smsQuotaService) {
        $user = $request->user();
        $logs = SmsLog::where('user_id', $user->id)->latest()->get();
        $alertsEnabled = $user->candidateProfile?->alerts_enabled ?? false;
        $smsRemaining = $smsQuotaService->remaining($user);
        $smsQuotaLimit = $smsQuotaService->quotaLimit($user);

        return view('candidate.sms', compact('logs', 'alertsEnabled', 'smsRemaining', 'smsQuotaLimit'));
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
    Route::get('/subscription', function (Request $request, SmsQuotaService $smsQuotaService) {
        $user = $request->user();
        $subscription = $user->activeSubscriptions()->with('plan')->first();
        $plans = Plan::where('active', true)->orderBy('price')->get();
        $smsRemaining = $smsQuotaService->remaining($user);
        $smsQuotaLimit = $smsQuotaService->quotaLimit($user);
        $pendingPayment = Payment::query()
            ->with('plan')
            ->where('user_id', $user->id)
            ->where('status', Payment::STATUS_PENDING)
            ->whereNotNull('payment_url')
            ->latest()
            ->first();

        return view('candidate.subscription', compact('subscription', 'plans', 'smsRemaining', 'smsQuotaLimit', 'pendingPayment'));
    })->name('subscription');
    Route::post('/subscription/change', function (Request $request, SubscriptionService $subscriptionService) {
        $validated = $request->validate(['plan_id' => ['required', 'exists:plans,id']]);
        $plan = Plan::query()->where('active', true)->findOrFail($validated['plan_id']);

        if (! $plan->isFree()) {
            return redirect()
                ->route('candidate.checkout', $plan)
                ->with('info', 'Finalisez le paiement pour activer cette formule.');
        }

        $subscriptionService->activate($request->user(), $plan);

        return redirect()->route('candidate.subscription')->with('status', 'plan-changed');
    })->name('subscription.change');

    Route::post('/skills', [CandidateSkillController::class, 'store'])->name('skills.store');
    Route::put('/skills/{candidateSkill:skill_id}', [CandidateSkillController::class, 'update'])->name('skills.update');
    Route::delete('/skills/{candidateSkill:skill_id}', [CandidateSkillController::class, 'destroy'])->name('skills.destroy');
});

// Routes publiques
Route::post('/webhooks/fiveonepay', FiveOnePayWebhookController::class)->name('webhooks.fiveonepay');

Route::get('/pricing', function () {
    $plans = Plan::query()->active()->orderBy('price')->get();

    return view('pricing', compact('plans'));
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
Route::get('/offers/search', [OfferController::class, 'search'])->name('offers.search');
Route::get('/offers/{offer}', [OfferController::class, 'show'])->name('offers.show');

if (app()->isLocal()) {
    Route::get('/__preview/{userId}', function (int $userId) {
        Auth::loginUsingId($userId);

        return redirect()->route(request()->query('to', 'dashboard'));
    })->middleware('web');
}
