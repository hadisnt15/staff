<?php

use Livewire\Component;

use App\Services\AttendanceService;
use App\Services\AttendanceSummaryService;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Computed]
    public function summary()
    {
        return AttendanceSummaryService::summary(
            auth()->id(),
            now()->format('Y-m')
        );
    }

    #[Computed]
    public function message()
    {
        return app(\App\Services\AttendanceService::class)->getMessage($this->summary);
    }
};
?>

<div>
    <div class="md:mt-4 mt-4 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 border border-emerald-600 rounded-xl md:px-4 md:py-2 px-2 py-1 text-sm text-emerald-700 text-center font-semibold md:mb-2
        @if($this->message['type'] === 'success') text-green-700
        @elseif($this->message['type'] === 'warning') text-yellow-700
        @elseif($this->message['type'] === 'danger') text-red-700
        @else text-gray-700
        @endif">
        {{-- ⏰ Anda tercatat telat beberapa kali bulan ini. Mari tingkatkan ketepatan waktu. --}}
        {{ $this->message['message'] }}
    </div>
</div>