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
    Route::livewire('/employee', 'pages::employee.index')->name('employee');

    // Khusus Super Admin & Manager
    // Route::middleware('role:super_admin|manager')->group(function () {
    //     Route::livewire('/employee', 'pages::employee.index')->name('employee');
    // });

    Route::post('/logout', function () {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

Route::livewire('/employee/presence', 'pages::employee.presence.index')->name('employee.presence');

Route::get('/test-cam', function () {
    return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Camera</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            padding:20px;
        }
        video{
            width:100%;
            max-width:500px;
            border:1px solid #ccc;
        }
        pre{
            background:#f5f5f5;
            padding:10px;
            white-space:pre-wrap;
        }
    </style>
</head>
<body>

<h2>Camera Test</h2>

<button onclick="startCamera()">Start Camera</button>

<video id="video" autoplay playsinline muted></video>

<pre id="log"></pre>

<script>
const log = (msg) => {
    document.getElementById('log').textContent += msg + "\n";
    console.log(msg);
};

log("UserAgent : " + navigator.userAgent);
log("Protocol : " + location.protocol);
log("Secure Context : " + isSecureContext);
log("mediaDevices : " + !!navigator.mediaDevices);
log("getUserMedia : " + !!navigator.mediaDevices?.getUserMedia);

async function startCamera(){

    log("========== START ==========");

    try {

        // cek permission browser
        if (navigator.permissions) {
            const permission = await navigator.permissions.query({
                name: "camera"
            });

            log("Camera permission state : " + permission.state);
        }

        // cek device
        const devices = await navigator.mediaDevices.enumerateDevices();

        devices.forEach(device => {
            log(
                device.kind +
                " | " +
                device.label +
                " | " +
                device.deviceId
            );
        });


        const stream = await navigator.mediaDevices.getUserMedia({
            video:true,
            audio:false
        });


        log("SUCCESS");

        document.getElementById("video").srcObject = stream;


    } catch(e){

        log("ERROR NAME : " + e.name);
        log("ERROR MSG  : " + e.message);

    }

}
</script>

</body>
</html>
HTML;
});