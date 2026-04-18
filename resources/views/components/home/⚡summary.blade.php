<?php

use Livewire\Component;

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

    public function render()
    {
        $chartData = [
            'workday' => (int) $this->summary['workdayCount'],
            'presence' => (int) $this->summary['presenceCount'],
            'late' => (int) $this->summary['lateCount'],
            'leave' => (int) $this->summary['leaveCount'],
        ];
        
        return $this->view([
            'chartData' => $chartData
        ]);
    }
};
?>

<div>
    <div class="flex md:grid md:grid-cols-[3fr_1fr] gap-4 overflow-x-auto md:overflow-visible flex-nowrap snap-x snap-mandatory scroll-smooth">
        <div class="min-w-[85%] md:min-w-0 snap-start text-sm border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl md:py-4 py-2">
            <div class="border-b border-gray-500">
                <div class="flex justify-between px-2">
                    <span class="font-semibold text-gray-800">Jumlah Hari Kerja</span>
                    <span class="font-semibold text-md text-gray-800">{{ $this->summary['workdayCount'] }}/{{ $this->summary['dayCount'] }} Hari</span>
                </div>
            </div>
            <div class="mt-4 px-2">
                <div class="">
                    <span class="font-semibold text-gray-500">Jumlah Kehadiran</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 border border-gray-400">
                    <div class="bg-gray-500 h-2 rounded-full" style="width:{{ $this->summary['workdayCount'] > 0 ? round($this->summary['presenceCount'] / $this->summary['workdayCount'] * 100, 2) : 0 }}%"></div>
                </div>
                <div class="text-right">
                    <span class="font-bold text-md text-gray-600">{{ $this->summary['presenceCount'] }}/{{ $this->summary['workdayCount'] }} Hari ({{ $this->summary['workdayCount'] > 0 ? round($this->summary['presenceCount']/$this->summary['workdayCount']*100,2) : 0 }}%)</span>
                </div>
            </div>
            <div class="mt-2 px-2">
                <div class="">
                    <span class="font-semibold text-gray-500">Jumlah Tepat Waktu</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 border border-gray-400">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width:{{ $this->summary['presenceCount'] > 0 ? round($this->summary['ontimeCount'] / $this->summary['presenceCount'] * 100, 2) : 0 }}%"></div>
                </div>
                <div class="text-right">
                    <span class="font-bold text-md text-emerald-600">{{ $this->summary['ontimeCount'] }}/{{ $this->summary['presenceCount'] }} Hari ({{ $this->summary['presenceCount'] > 0 ? round($this->summary['ontimeCount']/$this->summary['presenceCount']*100,2) : 0 }}%)</span>
                </div>
            </div>
            <div class="mt-2 px-2">
                <div class="">
                    <span class="font-semibold text-gray-500">Jumlah Telat</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 border border-gray-400">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width:{{ $this->summary['presenceCount'] > 0 ? round($this->summary['lateCount'] / $this->summary['presenceCount'] * 100, 2) : 0 }}%"></div>
                </div>
                <div class="text-right">
                    <span class="font-bold text-md text-yellow-600">{{ $this->summary['lateCount'] }}/{{ $this->summary['presenceCount'] }} Hari ({{ $this->summary['presenceCount'] > 0 ? round($this->summary['lateCount']/$this->summary['presenceCount']*100,2) : 0 }}%)</span>
                </div>
            </div>
            <div class="mt-2 px-2">
                <div class="">
                    <span class="font-semibold text-gray-500">Jumlah Tidak Hadir</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 border border-gray-400">
                    <div class="bg-red-500 h-2 rounded-full" style="width:{{ $this->summary['workdayCount'] > 0 ? round($this->summary['leaveCount'] / $this->summary['workdayCount'] * 100, 2) : 0 }}%"></div>
                </div>
                <div class="text-right">
                    <span class="font-bold text-md text-red-600">{{ $this->summary['leaveCount'] }}/{{ $this->summary['workdayCount'] }} Hari ({{ $this->summary['workdayCount'] > 0 ? round($this->summary['leaveCount']/$this->summary['workdayCount']*100,2) : 0 }}%)</span>
                </div>
            </div>
        </div>
        <div class="min-w-[65%] md:min-w-0 snap-start text-sm border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl p-2">
            <div wire:ignore id="attendanceChartWrapper2" data-chart='@json($chartData)' class="flex items-center justify-center py-1">
                <div class="w-full max-w-[220px] h-[260px]">
                    <canvas id="attendanceChart2" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>