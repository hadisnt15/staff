<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/userLogin');
});

Route::livewire('/userLogin', 'pages::auth.login')->middleware('guest')->name('login');
Route::get('/admin/login', function () {
    return redirect()->route('login');
})->name('filament.admin.auth.login');

// Route::get('/logout', function () {
//     Auth::logout();
//     request()->session()->invalidate();
//     request()->session()->regenerateToken();
//     return redirect('/userLogin');
// });

Route::livewire('/home', 'pages::home.index')->middleware('auth')->name('home');
Route::livewire('/salary', 'pages::salary.index')->middleware('auth')->name('salary');
Route::livewire('/employee', 'pages::employee.index')->middleware('auth')->name('employee');
Route::livewire('/profile', 'pages::profile.index')->middleware('auth')->name('profile');
Route::livewire('/face-registration', 'pages::face-registration.index')->middleware('auth')->name('face-registration');
Route::livewire('/leave-plan', 'pages::leave-plan.index')->middleware('auth')->name('leave-plan');

Route::post('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/userLogin');
})->name('logout');
