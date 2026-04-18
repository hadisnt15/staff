<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;

new #[Title('Profil Saya')] class extends Component
{
    public $name;
    public $email;

    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    // 🔥 update profile
    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email'
        ]);

        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email
        ]);

        session()->flash('success', 'Profil berhasil diperbarui');
    }

    // 🔥 update password
    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|same:new_password_confirmation',
        ]);

        if (!Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Password lama salah');
            return;
        }

        auth()->user()->update([
            'password' => Hash::make($this->new_password)
        ]);

        // reset field
        $this->reset([
            'current_password',
            'new_password',
            'new_password_confirmation'
        ]);

        session()->flash('success', 'Password berhasil diperbarui');
    }

    public function render()
    {
        return $this->view([
        ]);
    }
};
?>

<div class="md:p-4 px-4 py-2 mx-auto max-w-7xl">
    <div class="relative overflow-x-auto shadow-md rounded-md border border-gray-200 py-2 px-2 bg-white">
        @if (session()->has('success'))
            <div class="bg-green-100 text-green-700 p-2 rounded mb-2">
                {{ session('success') }}
            </div>
        @endif
        <nav class="flex mb-4 px-5 py-3 border rounded-md bg-emerald-50 border-gray-200" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="ms-1 text-sm font-medium text-emerald-800 md:ms-2">
                            Profil Saya
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="grid md:grid-cols-2 gap-2">
            <div class="border border-gray-300 shadow rounded p-4">
                <h2 class="font-semibold text-emerald-800 text-md mb-4">Perbarui Profil</h2>
    
                <form wire:submit.prevent="updateProfile" class="space-y-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" wire:model="name" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring focus:ring-emerald-200 w-full p-2.5">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model="email" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring focus:ring-emerald-200 w-full p-2.5">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
    
                    <button class="w-full bg-emerald-800 hover:bg-emerald-600 font-medium text-white rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan Profil
                    </button>
                </form>
            </div>
            <div class="border border-gray-300 shadow rounded p-4">
                <h2 class="font-semibold text-emerald-800 text-md mb-4">Perbarui Password</h2>
    
                <form wire:submit.prevent="updatePassword" class="space-y-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Password Lama</label>
                        <input type="password" wire:model="current_password" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring focus:ring-emerald-200 w-full p-2.5">
                        @error('current_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Password Baru</label>
                        <input type="password" wire:model="new_password" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring focus:ring-emerald-200 w-full p-2.5">
                        @error('new_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input type="password" wire:model="new_password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring focus:ring-emerald-200 w-full p-2.5">
                    </div>
    
                    <button class="w-full bg-emerald-800 hover:bg-emerald-600 font-medium text-white rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>