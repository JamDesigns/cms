<?php

use Laravel\Fortify\Features;
use Filament\Facades\Filament;
use Laravel\Fortify\RoutePath;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CategoryController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Laravel\Jetstream\Http\Controllers\CurrentTeamController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Jetstream\Http\Controllers\Livewire\TeamController;
use Laravel\Jetstream\Http\Controllers\TeamInvitationController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Jetstream\Http\Controllers\Livewire\ApiTokenController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Jetstream\Http\Controllers\Livewire\UserProfileController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Jetstream\Http\Controllers\Livewire\PrivacyPolicyController;
use Laravel\Jetstream\Http\Controllers\Livewire\TermsOfServiceController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath'
    ]
], function() {
    Route::get(LaravelLocalization::transRoute('/home'), function () {
        return view('welcome');
    })->name('home');
    Route::get(LaravelLocalization::transRoute('categories/{category:slug}'), [CategoryController::class, 'show'])->name('category.show');
    // Generated pages
    foreach (PageController::getPages() as $page) {
        Route::get(LaravelLocalization::transRoute($page->code), [PageController::class, 'show'])->name($page->code);
    };
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get(LaravelLocalization::transRoute('dashboard'), function () {
        return Redirect('/' . Filament::getPanel()->getPath());
    })->name('dashboard');
});

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        'web',
    ],
], function () {
    $enableViews = config('fortify.views', true);

    // Authentication...
    if ($enableViews) {
        Route::get(RoutePath::for('login', LaravelLocalization::transRoute('login')) , [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('login');
    }

    $limiter = config('fortify.limiters.login');
    $twoFactorLimiter = config('fortify.limiters.two-factor');
    $verificationLimiter = config('fortify.limiters.verification', '6,1');

    Route::post(RoutePath::for('login', LaravelLocalization::transRoute('login')) , [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest:' . config('fortify.guard'),
            $limiter ? 'throttle:' . $limiter : null,
        ]));

    Route::post(RoutePath::for('logout', LaravelLocalization::transRoute('logout')) , [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Password Reset...
    if (Features::enabled(Features::resetPasswords())) {
        if ($enableViews) {
            Route::get(RoutePath::for('password.request', LaravelLocalization::transRoute('forgot-password')) , [PasswordResetLinkController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('password.request');

            Route::get(RoutePath::for('password.reset', LaravelLocalization::transRoute('reset-password/{token}')) , [NewPasswordController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('password.reset');
        }

        Route::post(RoutePath::for('password.email', LaravelLocalization::transRoute('forgot-password')) , [PasswordResetLinkController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('password.email');

        Route::post(RoutePath::for('password.update', LaravelLocalization::transRoute('reset-password')) , [NewPasswordController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('password.update');
    }

    // Registration...
    if (Features::enabled(Features::registration())) {
        if ($enableViews) {
            Route::get(RoutePath::for('register', LaravelLocalization::transRoute('register')) , [RegisteredUserController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('register');
        }

        Route::post(RoutePath::for('register', LaravelLocalization::transRoute('register')) , [RegisteredUserController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')]);
    }

    // Email Verification...
    if (Features::enabled(Features::emailVerification())) {
        if ($enableViews) {
            Route::get(RoutePath::for('verification.notice', LaravelLocalization::transRoute('email/verify')) , [EmailVerificationPromptController::class, '__invoke'])
                ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
                ->name('verification.notice');
        }

        Route::get(RoutePath::for('verification.verify', LaravelLocalization::transRoute('email/verify/{id}/{hash}')) , [VerifyEmailController::class, '__invoke'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard'), 'signed', 'throttle:' . $verificationLimiter])
            ->name('verification.verify');

        Route::post(RoutePath::for('verification.send', LaravelLocalization::transRoute('email/verification-notification')) , [EmailVerificationNotificationController::class, 'store'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard'), 'throttle:' . $verificationLimiter])
            ->name('verification.send');
    }

    // Profile Information...
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::put(RoutePath::for('user-profile-information.update', LaravelLocalization::transRoute('user/profile-information')) , [ProfileInformationController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
            ->name('user-profile-information.update');
    }

    // Passwords...
    if (Features::enabled(Features::updatePasswords())) {
        Route::put(RoutePath::for('user-password.update', LaravelLocalization::transRoute('user/password')) , [PasswordController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
            ->name('user-password.update');
    }

    // Password Confirmation...
    if ($enableViews) {
        Route::get(RoutePath::for('password.confirm', LaravelLocalization::transRoute('user/confirm-password')) , [ConfirmablePasswordController::class, 'show'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')]);
    }

    Route::get(RoutePath::for('password.confirmation', LaravelLocalization::transRoute('user/confirmed-password-status')) , [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
        ->name('password.confirmation');

    Route::post(RoutePath::for('password.confirm', LaravelLocalization::transRoute('user/confirm-password')) , [ConfirmablePasswordController::class, 'store'])
        ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
        ->name('password.confirm');

    // Two Factor Authentication...
    if (Features::enabled(Features::twoFactorAuthentication())) {
        if ($enableViews) {
            Route::get(RoutePath::for('two-factor.login', LaravelLocalization::transRoute('two-factor-challenge')) , [TwoFactorAuthenticatedSessionController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('two-factor.login');
        }

        Route::post(RoutePath::for('two-factor.login', LaravelLocalization::transRoute('two-factor-challenge')) , [TwoFactorAuthenticatedSessionController::class, 'store'])
            ->middleware(array_filter([
                'guest:' . config('fortify.guard'),
                $twoFactorLimiter ? 'throttle:' . $twoFactorLimiter : null,
            ]));

        $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
        ? [config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard'), 'password.confirm']
        : [config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')];

        Route::post(RoutePath::for('two-factor.enable', LaravelLocalization::transRoute('user/two-factor-authentication')) , [TwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.enable');

        Route::post(RoutePath::for('two-factor.confirm', LaravelLocalization::transRoute('user/confirmed-two-factor-authentication')) , [ConfirmedTwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.confirm');

        Route::delete(RoutePath::for('two-factor.disable', LaravelLocalization::transRoute('user/two-factor-authentication')) , [TwoFactorAuthenticationController::class, 'destroy'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.disable');

        Route::get(RoutePath::for('two-factor.qr-code', LaravelLocalization::transRoute('user/two-factor-qr-code')) , [TwoFactorQrCodeController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.qr-code');

        Route::get(RoutePath::for('two-factor.secret-key', LaravelLocalization::transRoute('user/two-factor-secret-key')) , [TwoFactorSecretKeyController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.secret-key');

        Route::get(RoutePath::for('two-factor.recovery-codes', LaravelLocalization::transRoute('user/two-factor-recovery-codes')) , [RecoveryCodeController::class, 'index'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.recovery-codes');

        Route::post(RoutePath::for('two-factor.recovery-codes', LaravelLocalization::transRoute('user/two-factor-recovery-codes')) , [RecoveryCodeController::class, 'store'])
            ->middleware($twoFactorMiddleware);
    }
});

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        'web',
    ],
], function () {
    if (Jetstream::hasTermsAndPrivacyPolicyFeature()) {
        Route::get(LaravelLocalization::transRoute('terms-of-service'), [TermsOfServiceController::class, 'show'])->name('terms.show');
        Route::get(LaravelLocalization::transRoute('privacy-policy'), [PrivacyPolicyController::class, 'show'])->name('policy.show');
    }

    $authMiddleware = config('jetstream.guard')
    ? 'auth:' . config('jetstream.guard')
    : 'auth';

    $authSessionMiddleware = config('jetstream.auth_session', false)
    ? config('jetstream.auth_session')
    : null;

    Route::group(['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware]))], function () {
        // User & Profile...
        Route::get(LaravelLocalization::transRoute('user/profile'), [UserProfileController::class, 'show'])->name('profile.show');

        Route::group(['middleware' => 'verified'], function () {
            // API...
            if (Jetstream::hasApiFeatures()) {
                Route::get(LaravelLocalization::transRoute('user/api-tokens'), [ApiTokenController::class, 'index'])->name('api-tokens.index');
            }

            // Teams...
            if (Jetstream::hasTeamFeatures()) {
                Route::get(LaravelLocalization::transRoute('teams/create'), [TeamController::class, 'create'])->name('teams.create');
                Route::get(LaravelLocalization::transRoute('teams/{team}'), [TeamController::class, 'show'])->name('teams.show');
                Route::put(LaravelLocalization::transRoute('current-team'), [CurrentTeamController::class, 'update'])->name('current-team.update');

                Route::get(LaravelLocalization::transRoute('team-invitations/{invitation}'), [TeamInvitationController::class, 'accept'])
                    ->middleware(['signed'])
                    ->name('team-invitations.accept');
            }
        });
    });
});
