<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <div class="mx-auto grid md:grid-cols-2 gap-2 mt-4 block md:hidden">
        <div class="border-b border-gray-500">
            <span class="font-bold text-gray-500 text-sm px-4">Potongan</span>
            @foreach ($this->salaryResult['penalties'] as $salary)
                <div class="border-b border-gray-300 text-sm px-4">
                    <p class="font-semibold text-gray-500">{{ $salary['name'] }}</p>
                    <p class="font-semibold text-yellow-600 text-right">Rp {{ number_format($salary['amount'], 0, ',', '.') }}</p>
                </div>
            @endforeach
            <div class="flex justify-between font-bold text-sm px-4 mb-2">
                <span class="text-gray-500">Total Potongan</span>
                <span class="text-yellow-600 border-t border-gray-800">Rp {{ number_format($this->salaryResult['total_penalty']) }}</span>
            </div>
        </div>
    </div>

    <div class="md:block hidden">
        <div class="mx-auto grid grid-cols-2 gap-2 my-2 px-4">
            <div class="px-4">
                <span class="font-bold text-gray-500 text-sm">Potongan</span>
                @foreach ($this->salaryResult['penalties'] as $salary)
                    <div class="flex justify-between border-b border-gray-300 text-sm py-1">
                        <span class="font-semibold text-gray-500">{{ $salary['name'] }}</span>
                        <span class="font-semibold text-yellow-600">Rp {{ number_format($salary['amount'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-bold text-sm">
                    <span class="text-gray-500">Total Potongan</span>
                    <span class="text-yellow-600 border-t border-gray-800">Rp {{ number_format($this->salaryResult['total_penalty']) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>