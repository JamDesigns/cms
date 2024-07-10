<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Redirect('/' . Filament::getPanel()->getPath());
    })->name('dashboard');
});
