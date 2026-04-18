<?php

use Livewire\Component;

use App\Services\AttendanceSummaryService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;

new class extends Component
{
    #[Reactive]
    public $selectedUser = null;

    #[Reactive]
    public $selectedPeriod = null;

    #[Computed]
    public function userSummaries()
    {
        if (empty($this->selectedUser) || empty($this->selectedPeriod)) {
            return [
                'dayCount' => 0,
                'presenceCount' => 0,
                'lateCount' => 0,
                'ontimeCount' => 0,
                'leaveCount' => 0,
                'workdayCount' => 0,
                'workdayCountNow' => 0,
                'holidayCount' => 0,
            ];
        }
        return AttendanceSummaryService::summary(
            (int) $this->selectedUser,
            $this->selectedPeriod
        );
    }

    #[Computed]
    public function attendanceDetails()
    {
        if (empty($this->selectedUser) || empty($this->selectedPeriod)) {
            return collect();
        }
        return AttendanceSummaryService::baseQuery(
            (int) $this->selectedUser,
            $this->selectedPeriod
        )->orderByDesc('tanggal')->get();
    }

    public function render()
    {
        $summary = $this->userSummaries;
        // dd($this->attendanceDetails);
        $chartData = [
            'workday' => (int) $summary['workdayCount'],
            'presence' => (int) $summary['presenceCount'],
            'late' => (int) $summary['lateCount'],
            'leave' => (int) $summary['leaveCount'],
        ];

        return $this->view([
            'chartData' => $chartData
        ]);
    }
};
?>

<div>
    <h2 class="text-sm font-bold text-gray-800 text-center px-4">
        Rekap Presensi
    </h2>
    <div class="flex md:grid md:grid-cols-2 gap-2 overflow-x-auto md:overflow-visible flex-nowrap snap-x snap-mandatory scroll-smooth">
        <div class="min-w-[90%] md:min-w-0 snap-start text-sm border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl md:py-4 py-2">
            <div class="border-b border-emerald-600">
                <div class="flex justify-between px-2">
                    <span class="font-semibold text-gray-800">Jumlah Hari Kerja</span>
                    <span class="font-semibold text-md text-gray-800">{{ $this->userSummaries['workdayCount'] }}/{{ $this->userSummaries['dayCount'] }} Hari</span>
                </div>
            </div>
            <div class="mt-4 px-2">
                <div class="">
                    <span class="font-semibold text-gray-500">Jumlah Kehadiran</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 border border-gray-400">
                    <div class="bg-gray-500 h-2 rounded-full" style="width:{{ $this->userSummaries['workdayCount'] > 0 ? round($this->userSummaries['presenceCount'] / $this->userSummaries['workdayCount'] * 100, 2) : 0 }}%"></div>
                </div>
                <div class="text-right">
                    <span class="font-bold text-md text-gray-600">{{ $this->userSummaries['presenceCount'] }}/{{ $this->userSummaries['workdayCount'] }} Hari ({{ $this->userSummaries['workdayCount'] > 0 ? round($this->userSummaries['presenceCount']/$this->userSummaries['workdayCount']*100,2) : 0 }}%)</span>
                </div>
            </div>
            <div class="mt-2 px-2">
                <div class="">
                    <span class="font-semibold text-gray-500">Jumlah Tepat Waktu</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 border border-gray-400">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width:{{ $this->userSummaries['presenceCount'] > 0 ? round($this->userSummaries['ontimeCount'] / $this->userSummaries['presenceCount'] * 100, 2) : 0 }}%"></div>
                </div>
                <div class="text-right">
                    <span class="font-bold text-md text-emerald-600">{{ $this->userSummaries['ontimeCount'] }}/{{ $this->userSummaries['presenceCount'] }} Hari ({{ $this->userSummaries['presenceCount'] > 0 ? round($this->userSummaries['ontimeCount']/$this->userSummaries['presenceCount']*100,2) : 0 }}%)</span>
                </div>
            </div>
            <div class="mt-2 px-2">
                <div class="">
                    <span class="font-semibold text-gray-500">Jumlah Telat</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 border border-gray-400">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width:{{ $this->userSummaries['presenceCount'] > 0 ? round($this->userSummaries['lateCount'] / $this->userSummaries['presenceCount'] * 100, 2) : 0 }}%"></div>
                </div>
                <div class="text-right">
                    <span class="font-bold text-md text-yellow-600">{{ $this->userSummaries['lateCount'] }}/{{ $this->userSummaries['presenceCount'] }} Hari ({{ $this->userSummaries['presenceCount'] > 0 ? round($this->userSummaries['lateCount']/$this->userSummaries['presenceCount']*100,2) : 0 }}%)</span>
                </div>
            </div>
            <div class="mt-2 px-2">
                <div class="">
                    <span class="font-semibold text-gray-500">Jumlah Tidak Hadir</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 border border-gray-400">
                    <div class="bg-red-500 h-2 rounded-full" style="width:{{ $this->userSummaries['workdayCount'] > 0 ? round($this->userSummaries['leaveCount'] / $this->userSummaries['workdayCount'] * 100, 2) : 0 }}%"></div>
                </div>
                <div class="text-right">
                    <span class="font-bold text-md text-red-600">{{ $this->userSummaries['leaveCount'] }}/{{ $this->userSummaries['workdayCount'] }} Hari ({{ $this->userSummaries['workdayCount'] > 0 ? round($this->userSummaries['leaveCount']/$this->userSummaries['workdayCount']*100,2) : 0 }}%)</span>
                </div>
            </div>
        </div>
        <div class="min-w-[90%] md:min-w-0 snap-start text-sm border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl p-2">
            <div class="max-h-75 relative z-10 relative overflow-x-auto">
                <div class="grid md:grid-cols-2 gap-1">
                    @forelse ($this->attendanceDetails as $item)
                    <div class="p-2 border border-emerald-600 rounded-xl">
                        <div class="font-semibold">
                            {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('l, d F Y') }} ({{ $item->tanggal_merah }}) 
                        </div>
                        @if ($item->poin_kehadiran >= 0 && $item->poin_kehadiran <= 5)
                            <div>
                                Waktu Kerja: {{ $item->jam_datang ?? '-' }} s/d {{ $item->jam_pulang ?? '-' }} <br>
                                Istirahat: {{ $item->jam_mulai_istirahat ?? '-' }} s/d {{ $item->jam_selesai_istirahat ?? '-' }} 
                            </div>
                        @endif
                        <div>
                            Ket: {{ $item->ket_kehadiran }} 
                        </div>
                    </div>
                     @empty
                        <div class="col-span-full">
                            <div class="text-center p-2 text-sm text-red-600 font-semibold">
                                Data Kehadiran Tidak Ditemukan.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>