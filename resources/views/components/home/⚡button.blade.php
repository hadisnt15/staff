<?php

use Livewire\Component;

use App\Services\AttendanceService;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Computed]
    public function todayLog()
    {
        return AttendanceService::getTodayLog(auth()->id());
    }
    
    #[Computed]
    public function buttons()
    {
        return AttendanceService::getButtonState($this->todayLog, auth()->user());
    }
};
?>

<div>
    <div class="grid grid-cols-4 gap-3 mt-4">

        {{-- Masuk --}}
        <button wire:click="$dispatch('openCheckinModal')" @disabled($this->buttons['checkin']) title="{{ $this->buttons['checkin'] ? 'Tidak bisa absen masuk saat ini' : '' }}" class="group rounded-2xl border border-emerald-100 bg-white p-3 shadow-sm shadow-emerald-200 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-50 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100">
                <i class="ri-login-circle-fill text-2xl text-emerald-700"></i>
            </div>
            <p class="mt-2 text-[11px] md:text-sm font-semibold text-emerald-800">
                Masuk
            </p>
        </button>

        {{-- Keluar --}}
        <button wire:click="$dispatch('openCheckoutModal')" @disabled($this->buttons['checkout']) title="{{ $this->buttons['checkout'] ? 'Tidak bisa absen keluar saat ini' : '' }}" class="group rounded-2xl border border-emerald-100 bg-white p-3 shadow-sm shadow-emerald-200 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-50 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-red-50">
                <i class="ri-logout-circle-fill text-2xl text-red-500"></i>
            </div>
            <p class="mt-2 text-[11px] md:text-sm font-semibold text-emerald-800">
                Keluar
            </p>
        </button>

        {{-- Luar Kota --}}
        <button wire:click="$dispatch('openBusinessTripModal')" @disabled($this->buttons['businessTrip']) title="{{ $this->buttons['businessTrip'] ? 'Hanya untuk Driver & Salesman' : '' }}" class="group rounded-2xl border border-emerald-100 bg-white p-3 shadow-sm shadow-emerald-200 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-50 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50">
                <i class="ri-compass-3-fill text-2xl text-sky-500"></i>
            </div>
            <p class="mt-2 text-[11px] md:text-sm font-semibold text-emerald-800">
                Luar Kota
            </p>
        </button>

        {{-- Tidak Hadir --}}
        <button wire:click="$dispatch('openLeaveModal')" @disabled($this->buttons['leave']) title="{{ $this->buttons['leave'] ? 'Anda sudah hadir hari ini' : '' }}" class="group rounded-2xl border border-emerald-100 bg-white p-3 shadow-sm shadow-emerald-200 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-50 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50">
                <i class="ri-indeterminate-circle-fill text-2xl text-amber-500"></i>
            </div>
            <p class="mt-2 text-[11px] md:text-sm font-semibold text-emerald-800">
                Tidak Hadir
            </p>
        </button>

    </div>
</div>