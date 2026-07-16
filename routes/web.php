<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/userLogin');

Route::middleware('guest')->group(function () {
    Route::livewire('/userLogin', 'pages::auth.login')
        ->name('login');
});

Route::redirect('/admin/login', '/userLogin')
    ->name('filament.admin.auth.login');

Route::middleware('auth')->group(function () {

    // Halaman umum
    Route::livewire('/home', 'pages::home.index')->name('home');
    Route::livewire('/salary', 'pages::salary.index')->name('salary');
    Route::livewire('/profile', 'pages::profile.index')->name('profile');
    Route::livewire('/face-registration', 'pages::face-registration.index')->name('face-registration');
    Route::livewire('/leave-plan', 'pages::leave-plan.index')->name('leave-plan');

    // Khusus Super Admin & Manager
    Route::middleware('role:super_admin|manager')->group(function () {
        Route::livewire('/employee', 'pages::employee.index')
            ->name('employee');
    });

    Route::post('/logout', function () {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});