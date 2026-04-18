<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Masuk')] class extends Component
{
    public $email;
    public $password;

    // public function title(): string
    // {
    //     return 'Masuk - StaffPort';
    // }

    public function login()
    {
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials)) {

            session()->regenerate();

            return redirect('/home');
        }

        $this->addError('email', 'Email atau password salah');
    }
};
?>

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-4">
    <div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200 py-4 px-6 bg-white">
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-4">
            <div class="px-2 py-6 text-center">
                <p class="text-sm sm:text-base text-gray-600 font-medium">
                    Selamat Datang di
                </p>
    
                <h1 class="mt-1 font-extrabold text-gray-900
                        text-3xl sm:text-4xl md:text-5xl lg:text-6xl leading-tight">
                    StaffPort
                </h1>
    
                <h2 class="mt-1 font-bold text-emerald-800
                        text-xl sm:text-2xl md:text-3xl lg:text-4xl tracking-wide">
                    PT Kapuas Kencana Jaya
                </h2>
    
                <!-- Accent line -->
                <div class="mx-auto mt-4 w-16 h-1 bg-emerald-800 rounded-full"></div>
            </div>
    
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                <div class="hidden md:flex relative overflow-hidden rounded-xl">
    
                    <!-- Decorative vertical line -->
                    <div class="absolute right-8 top-0 h-full w-px bg-gradient-to-b from-transparent via-green-700 to-transparent opacity-30"></div>
    
                    <!-- Content -->
                    <div class="relative z-10 p-10 flex flex-col justify-center">
                        
                        <h2 class="text-2xl font-bold text-gray-800 leading-snug">
                            Digitalisasi dan Automasi Manajemen Karyawan dalam Satu Platform
                        </h2>
    
                        <p class="mt-4 text-sm text-gray-600 max-w-sm leading-relaxed">
                            Staff Management Portal membantu pengelolaan karyawan dengan lebih mudah, efisien, dan terstruktur. Mengurangi pekerjaan manual dan berulang, sekaligus meningkatkan akurasi dan produktivitas dalam setiap proses pengelolaan karyawan.
                        </p>
    
                        <!-- Divider -->
                        <div class="mt-6 w-16 h-1 bg-emerald-800 rounded-full"></div>
    
                        <!-- Keywords -->
                        <div class="mt-6 flex flex-wrap gap-2 text-xs font-semibold text-gray-500">
                            <span class="px-3 py-1 border border-gray-300 rounded-full">Automation</span>
                            <span class="px-3 py-1 border border-gray-300 rounded-full">Efficient</span>
                            <span class="px-3 py-1 border border-gray-300 rounded-full">Flexible</span>
                        </div>
    
                    </div>
    
                    <!-- Subtle background shape -->
                    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-800/5 rounded-full"></div>
                </div>
                <div>
                    <div id="accordion-collapse" data-accordion="collapse"
                        class="rounded-xl overflow-hidden shadow-md ">
                        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                            <form wire:submit.prevent="login" class="space-y-4 md:space-y-6">
                                <div>
                                    <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" wire:model="email"
                                        class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5 text-gray-700 placeholder-gray-400 focus:ring focus:ring-indigo-200 focus:border-indigo-300"
                                        placeholder="Email" required autocomplete="off" autofocus>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Kata Sandi</label>
                                    <input type="password" wire:model="password"
                                        class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5 text-gray-700 placeholder-gray-400 focus:ring focus:ring-indigo-200 focus:border-indigo-300"
                                        placeholder="••••••••" required autocomplete="off">
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button class="w-full mt-3 bg-emerald-800 hover:bg-emerald-600 text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    Masuk
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>