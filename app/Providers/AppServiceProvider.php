<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Statikbe\FilamentTranslationManager\FilamentTranslationManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(app()->getLocale());
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
        $switch
            ->locales([
                'es',
                'en',
                // 'fr',
            ]); // also accepts a closure
        });
        FilamentTranslationManager::setLocales([
            'es',
            'en',
            // 'fr',
        ]);
    }
}
