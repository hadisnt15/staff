<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <li>
        <a href="/admin" class="block py-2 px-4 text-sm hover:bg-gray-300 hover:text-emerald-800"><i class="ri-admin-fill"></i> Kelola Data</a>
    </li>
    <li>
        <a href="{{ route('employee') }}" class="block py-2 px-4 text-sm hover:bg-gray-300 hover:text-emerald-800"><i class="ri-team-fill"></i> Rekap Data Karyawan</a>
    </li>
</div>