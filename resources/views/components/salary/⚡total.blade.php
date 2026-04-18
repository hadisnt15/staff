<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <div class="mx-auto grid md:grid-cols-2 gap-2 mt-4 block md:hidden">
        <div class="flex justify-between text-sm px-4">
            <span class="font-bold text-gray-800">Gaji Bersih</span>
            <span class="font-bold text-green-800">Rp {{ number_format($this->salaryResult['final']) }}</span>
        </div>
        <div class="text-center text-sm">
            <span class="font-semibold text-gray-800">Terbilang</span>
            <span class="font-semibold text-green-800">{{ ucfirst(trim(terbilang($this->salaryResult['final']))) }} Rupiah</span>
        </div>
    </div>

    <div class="md:block hidden">
        <div class="border-t border-gray-400">
            <div class="px-4 mt-2">
                <div class="flex justify-between border-b border-gray-300 text-sm ">
                    <span class="font-bold text-gray-800">Gaji Bersih</span>
                    <span class="font-bold text-green-800">Rp {{ number_format($this->salaryResult['final']) }}</span>
                </div>
                <div class="text-center text-sm mt-2">
                    <span class="font-bold text-gray-800 border-b border-gray-300">Terbilang</span>
                    <span class="font-bold text-green-800 border-b border-gray-300">{{ ucfirst(trim(terbilang($this->salaryResult['final']))) }} Rupiah</span>
                </div>
            </div>
        </div>
    </div>
</div>