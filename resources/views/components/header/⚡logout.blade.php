<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public function logout()
    {
        Auth::logout();

        // session()->invalidate();
        // session()->regenerateToken();

        $this->redirect('/userLogin');
    }
};
?>

<div>
    <li>
        <button wire:click="logout" class="block py-2 px-4 text-sm hover:bg-gray-300 hover:text-emerald-800 text-gray-100 rounded-b w-full text-left">
            <i class="ri-logout-box-r-fill"></i> Keluar
        </button>
    </li>
</div>