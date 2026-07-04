<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return redirect('/userLogin');
});

Route::livewire('/userLogin', 'pages::auth.login')->middleware('guest')->name('login');
// Route::get('/admin/login', function () {
//     return redirect()->route('login');
// })->name('filament.admin.auth.login');

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

Route::post('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/userLogin');
})->name('logout');

Route::get('/cek-user', function () {
    return [
        'auth' => auth()->check(),
        'id' => auth()->id(),
        'email' => auth()->user()?->email,
        'role' => auth()->user()?->roles->pluck('name'),
    ];
});