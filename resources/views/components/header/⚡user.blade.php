<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <li>
        <a href="{{ route('profile') }}" class="block py-2 px-4 text-sm hover:bg-gray-300 hover:text-emerald-800"><i class="ri-account-circle-2-fill"></i> Profil Saya</a>
    </li>
    <div class="border-t border-gray-200 my-1"></div>
    <li>
        <a href="{{ route('home') }}" class="block py-2 px-4 text-sm hover:bg-gray-300 hover:text-emerald-800"><i class="ri-user-follow-fill"></i> Halaman Utama</a>
    </li>
    <li>
        <a href="{{ route('face-registration') }}" class="block py-2 px-4 text-sm hover:bg-gray-300 hover:text-emerald-800"><i class="ri-qr-scan-ai-line"></i> Registrasi Wajah</a>
    </li>
    <li>
        <a href="{{ route('salary') }}" class="block py-2 px-4 text-sm hover:bg-gray-300 hover:text-emerald-800"><i class="ri-calculator-fill"></i> Gaji Saya</a>
    </li>
</div>