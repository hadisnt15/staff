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
    {{-- tombol presensi desktop --}}
    <div class="md:block hidden">
        <div class="grid grid-cols-4 mt-4 gap-4">
            <div>
                <button
                    wire:click="$dispatch('openCheckinModal')" @disabled($this->buttons['checkin']) title="{{ $this->buttons['checkin'] ? 'Tidak bisa absen masuk saat ini' : '' }}" 
                    class="bg-primary hover:bg-emerald-800 w-full h-20 flex items-center justify-center border border-emerald-50 rounded-base shadow-lg text-xl text-white {{ $this->buttons['checkin'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <i class="ri-login-circle-fill text-5xl text-neutral-primary-soft"></i> 
                    <span class="font-bold px-2">Masuk</span>
                </button>
            </div>
            <div>
                <button
                    wire:click="$dispatch('openCheckoutModal')" @disabled($this->buttons['checkout']) title="{{ $this->buttons['checkout'] ? 'Tidak bisa absen keluar saat ini' : '' }}" 
                    class="bg-primary hover:bg-emerald-800 w-full h-20 flex items-center justify-center border border-emerald-50 rounded-base shadow-lg text-xl text-white {{ $this->buttons['checkout'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <i class="ri-logout-circle-fill text-5xl text-neutral-primary-soft"></i> 
                    <span class="font-bold px-2">Keluar</span>
                </button>
            </div>
            <div>
                <button
                    wire:click="$dispatch('openBusinessTripModal')" @disabled($this->buttons['businessTrip']) title="{{ $this->buttons['businessTrip'] ? 'Hanya untuk Driver & Salesman' : '' }}" 
                    class="bg-primary hover:bg-emerald-800 w-full h-20 flex items-center justify-center border border-emerald-50 rounded-base shadow-lg text-xl text-white {{ $this->buttons['businessTrip'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <i class="ri-compass-3-fill text-5xl text-neutral-primary-soft"></i> 
                    <span class="font-bold px-2">Luar Kota</span>
                </button>
            </div>
            <div>
                <button
                    wire:click="$dispatch('openLeaveModal')" @disabled($this->buttons['leave']) title="{{ $this->buttons['leave'] ? 'Anda sudah hadir hari ini' : '' }}"
                    class="bg-primary hover:bg-emerald-800 w-full h-20 flex items-center justify-center border border-emerald-50 rounded-base shadow-lg text-xl text-white {{ $this->buttons['leave'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <i class="ri-indeterminate-circle-fill text-5xl text-neutral-primary-soft"></i> 
                    <span class="font-bold px-2">Tidak Hadir</span>
                </button>
            </div>
        </div>
    </div>
    {{-- tombol presensi desktop end --}}

    {{-- tombol presensi mobile --}}
    <div class="md:hidden block">
        <div class="grid grid-cols-4 mt-2 gap-4">
            <div class="flex flex-col items-center justify-center text-center">
                <button wire:click="$dispatch('openCheckinModal')" @disabled($this->buttons['checkin']) title="{{ $this->buttons['checkin'] ? 'Tidak bisa absen masuk saat ini' : '' }}" class="{{ $this->buttons['checkin'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <div class="bg-primary hover:bg-emerald-800 w-20 h-15 flex items-center justify-center border border-emerald-50 rounded-2xl shadow-lg">
                        <i class="ri-login-circle-fill text-4xl text-neutral-primary-soft"></i>
                    </div>
                </button>
                <span class="text-xs font-bold text-primary mt-1">Masuk</span>
            </div>
            <div class="flex flex-col items-center justify-center text-center">
                <button wire:click="$dispatch('openCheckoutModal')" @disabled($this->buttons['checkout']) title="{{ $this->buttons['checkout'] ? 'Tidak bisa absen keluar saat ini' : '' }}" class="{{ $this->buttons['checkout'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <div class="bg-primary hover:bg-emerald-800 w-20 h-15 flex items-center justify-center border border-emerald-50 rounded-2xl shadow-lg">
                        <i class="ri-logout-circle-fill text-4xl text-neutral-primary-soft"></i>
                    </div>
                </button>
                <span class="text-xs font-bold text-primary mt-1">Keluar</span>
            </div>
            <div class="flex flex-col items-center justify-center text-center">
                <button wire:click="$dispatch('openBusinessTripModal')" @disabled($this->buttons['businessTrip']) title="{{ $this->buttons['businessTrip'] ? 'Hanya untuk Driver & Salesman' : '' }}" class="{{ $this->buttons['businessTrip'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <div class="bg-primary hover:bg-emerald-800 w-20 h-15 flex items-center justify-center border border-emerald-50 rounded-2xl shadow-lg">
                        <i class="ri-compass-3-fill text-4xl text-neutral-primary-soft"></i>
                    </div>
                </button>
                <span class="text-xs font-bold text-primary mt-1">Luar Kota</span>
            </div>
            <div class="flex flex-col items-center justify-center text-center">
                <button wire:click="$dispatch('openLeaveModal')"  @disabled($this->buttons['leave']) title="{{ $this->buttons['leave'] ? 'Hanya untuk Driver & Salesman' : '' }}" class="{{ $this->buttons['leave'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <div class="bg-primary hover:bg-emerald-800 w-20 h-15 flex items-center justify-center border border-emerald-50 rounded-2xl shadow-lg">
                        <i class="ri-indeterminate-circle-fill text-4xl text-neutral-primary-soft"></i>
                    </div>
                </button>
                <span class="text-xs font-bold text-primary mt-1">Tidak Hadir</span>
            </div>
            
        </div>
    </div>
    {{-- tombol presensi mobile end --}} 
</div>