<?php

use Livewire\Component;

use App\Services\AttendanceService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new class extends Component
{
    public array $buttons = [];

    #[Computed]
    public function todayLog()
    {
        return AttendanceService::getTodayLog(auth()->id());
    }

    public function openCheckinModal()
    {
        $this->dispatch('openCheckinModal');
    }
    
    public function openCheckoutModal()
    {
        $this->dispatch('openCheckoutModal');
    }
    
    public function openBusinessTripModal()
    {
        $this->dispatch('openBusinessTripModal');
    }
    
    public function openLeaveModal()
    {
        $this->dispatch('openLeaveModal');
    }

    public function mount()
    {
        $this->refreshButtons();
    }

    public function refreshButtons()
    {
        $todayLog = AttendanceService::getTodayLog(auth()->id());

        $this->buttons = AttendanceService::getButtonState(
            $todayLog,
            auth()->user()
        );
    }

    #[On('attendance-saved')]
    public function loadButtons()
    {
        $this->refreshButtons();
    }
};
?>

<div>
    <div class="grid grid-cols-4 gap-3 mt-4">
        <button wire:click="openCheckinModal" @disabled($this->buttons['checkin']) title="{{ $this->buttons['checkin'] ? 'Tidak bisa absen masuk saat ini' : '' }}" class="group rounded-2xl border border-emerald-100 bg-white p-3 shadow-sm shadow-emerald-200 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-50 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100">
                <i wire:loading.remove wire:target="openCheckinModal" class="ri-login-circle-fill text-2xl text-emerald-700"></i>
                <i wire:loading wire:target="openCheckinModal" class="ri-loader-4-line animate-spin"></i>
            </div>
            <p class="mt-2 text-[11px] md:text-sm font-semibold text-emerald-800">Masuk</p>
        </button>
        <button wire:click="openCheckoutModal" @disabled($this->buttons['checkout']) title="{{ $this->buttons['checkout'] ? 'Tidak bisa absen keluar saat ini' : '' }}" class="group rounded-2xl border border-emerald-100 bg-white p-3 shadow-sm shadow-emerald-200 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-50 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-red-50">
                <i wire:loading.remove wire:target="openCheckoutModal" class="ri-logout-circle-fill text-2xl text-red-500"></i>
                <i wire:loading wire:target="openCheckoutModal" class="ri-loader-4-line animate-spin"></i>
            </div>
            <p class="mt-2 text-[11px] md:text-sm font-semibold text-emerald-800">Keluar</p>
        </button>
        <button wire:click="openBusinessTripModal" @disabled($this->buttons['businessTrip']) title="{{ $this->buttons['businessTrip'] ? 'Hanya untuk Driver & Salesman' : '' }}" class="group rounded-2xl border border-emerald-100 bg-white p-3 shadow-sm shadow-emerald-200 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-50 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50">
                <i wire:loading.remove wire:target="openBusinessTripModal" class="ri-compass-3-fill text-2xl text-sky-500"></i>
                <i wire:loading wire:target="openBusinessTripModal" class="ri-loader-4-line animate-spin"></i>
            </div>
            <p class="mt-2 text-[11px] md:text-sm font-semibold text-emerald-800">Luar Kota</p>
        </button>
        <button wire:click="openLeaveModal" @disabled($this->buttons['leave']) title="{{ $this->buttons['leave'] ? 'Anda sudah hadir hari ini' : '' }}" class="group rounded-2xl border border-emerald-100 bg-white p-3 shadow-sm shadow-emerald-200 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-50 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50">
                <i wire:loading.remove wire:target="openLeaveModal" class="ri-indeterminate-circle-fill text-2xl text-amber-500"></i>
                <i wire:loading wire:target="openLeaveModal" class="ri-loader-4-line animate-spin"></i>
            </div>
            <p class="mt-2 text-[11px] md:text-sm font-semibold text-emerald-800">Tidak Hadir</p>
        </button>

    </div>
</div>