<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\AttendanceSummaryService;
use Livewire\Attributes\Computed;

new class extends Component
{
    public $selectedYear;
    public $selectedMonth;
    public $attendanceDetails = [];
    public $showAttendanceModal = false;

    #[On('attendance-filter-changed')]
    public function loadData($year, $month)
    {
        $this->selectedYear = $year;
        $this->selectedMonth = $month;
    }

    #[Computed]
    public function data()
    {
        if (!$this->selectedYear || !$this->selectedMonth) {
            return collect();
        }

        return AttendanceSummaryService::allAttendanceSummary(
            $this->selectedYear, $this->selectedMonth
        );
    }

    public function openModal($userId, $tanggal)
    {
        $this->dispatch('open-attendance-detail-modal',  userId: $userId, tanggal: $tanggal);
    }

    public function mount()
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }
};
?>

<div>
   <div class="space-y-4">
        <h2 class="text-lg font-bold text-emerald-700 my-4 text-center">
            Presensi Karyawan Periode
            {{ \Carbon\Carbon::create($selectedYear, $selectedMonth)->locale('id')->translatedFormat('F Y') }}
        </h2>
        @forelse($this->data as $branch => $users)
            <div x-data="{ openBranch: true }" class="rounded-md border border-emerald-300 my-4 overflow-hidden">
                <!-- Header Cabang -->
                <button @click="openBranch=!openBranch" class="w-full flex justify-between items-center px-4 py-3 bg-emerald-600 text-white font-semibold">
                    <span>Cabang: {{ $branch }}</span>
                    <i class="ri-arrow-down-s-line transition" :class="{ 'rotate-180': openBranch }"></i>
                </button>
                <div x-show="openBranch" x-collapse class="p-3 bg-white">
                    @foreach($users as $userId => $items)
                    <div x-data="{ openUser: true }" class="rounded-md mb-3 border border-emerald-200 overflow-hidden">
                        <!-- Header User -->
                        <button @click="openUser=!openUser" class="w-full flex justify-between items-center px-3 py-2 bg-emerald-50 hover:bg-emerald-100">
                            <div class="flex items-center gap-2 flex-wrap text-sm">
                                <span class="font-semibold text-emerald-800">{{ $items->first()->user->name }}</span>
                                (@foreach($items->first()->user->roles as $role)
                                    <span class="text-xs text-gray-700">
                                        {{ $role->name }}@unless($loop->last), @endunless
                                    </span>
                                @endforeach)
                            </div>
                            <i class="ri-arrow-down-s-line transition" :class="{ 'rotate-180': openUser }"></i>
                        </button>
                        <div x-show="openUser" x-collapse>
                            <div class="max-h-96 overflow-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="sticky top-0 bg-white border-b border-emerald-100 z-10">
                                        <tr>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Tanggal</th>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Datang</th>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Pulang</th>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Mulai Istirahat</th>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Selesai Istirahat</th>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Mulai Izin</th>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Selesai Izin</th>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Luar Kota</th>
                                            <th class="px-3 py-2 text-left w-1/12 font-semibold">Tidak Masuk</th>
                                            <th class="px-3 py-2 text-left w-3/12 font-semibold">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-emerald-100">
                                        @foreach($items as $item)
                                            <tr wire:click="openModal('{{ $item->id_pengguna }}','{{ $item->tanggal }}')" class="cursor-pointer hover:bg-emerald-50">
                                                <td class="px-3 py-2 @if($item->tanggal_merah !== 'Hari Kerja') text-red-600 @endif">
                                                    {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d l') }}
                                                </td>
                                                <td class="px-3 py-2">{{ $item->jam_datang ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $item->jam_pulang ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $item->jam_mulai_istirahat ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $item->jam_selesai_istirahat ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $item->jam_mulai_izin ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $item->jam_selesai_izin ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $item->luar_kota ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $item->tidak_masuk ?? '-' }}</td>
                                                <td class="px-3 py-2">
                                                    @if($item->ket_kehadiran == 'Tidak hadir tanpa konfirmasi.' && $item->tanggal_merah !== 'Hari Kerja')
                                                        <span class="text-red-600">{{ $item->tanggal_merah }}</span>
                                                    @else
                                                        {{ $item->ket_kehadiran }}<br>
                                                        {{ $item->ket_kehadiran_rekap }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-gray-500">
                Data tidak ditemukan.
            </div>
        @endforelse
    </div>
    <livewire:employee.presence.modal />
</div>