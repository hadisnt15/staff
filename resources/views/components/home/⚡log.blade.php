<?php

use Livewire\Component;

use App\Services\AttendanceService;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Computed]
    public function type()
    {
        return AttendanceService::getType($this->todayLog);
    }
    
    #[Computed]
    public function todayLog()
    {
        return AttendanceService::getTodayLog(auth()->id());
    }
};
?>

<div>
    <div wire:poll class="px-2 py-0.5 border-b border-gray-200 text-sm">
        @if ($this->todayLog)
            <span class="text-emerald-700">
                Presensi terakhir hari ini: {{ $this->todayLog->attendance_datetime->format('H:i') }} ({{ $this->type }})
            </span>
        @else
            <span class="text-red-700">
                Presensi terakhir hari ini: -
            </span>
        @endif
    </div>
</div>