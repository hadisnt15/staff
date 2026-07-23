<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\AttendanceSummaryService;
use Livewire\Attributes\Computed;

new class extends Component
{
    public $selectedYear;
    public $selectedMonth;

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
};
?>

<div>
   <div class="space-y-4">
    @forelse ($this->data as $userId => $items)
        <div class="rounded-lg border border-emerald-200 overflow-hidden">
            <div class="px-3 py-2 bg-emerald-50 text-emerald-700 font-semibold text-sm">
                {{ $items->first()->user->name ?? 'Tanpa Nama' }}
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white text-gray-600 border-b border-emerald-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-left">Datang</th>
                            <th class="px-3 py-2 text-left">Pulang</th>
                            <th class="px-3 py-2 text-left">Mulai Istirahat</th>
                            <th class="px-3 py-2 text-left">Selesai Istirahat</th>
                            <th class="px-3 py-2 text-left">Mulai Izin</th>
                            <th class="px-3 py-2 text-left">Selesai Izin</th>
                            <th class="px-3 py-2 text-left">Ket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100">
                        @foreach ($items as $item)
                            <tr class="hover:bg-emerald-50">
                                <td class="px-3 py-2">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->jam_datang ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->jam_pulang ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->jam_mulai_istirahat ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->jam_selesai_istirahat ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->jam_mulai_izin ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->jam_selesai_izin ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    @if($item->ket_kehadiran == $item->ket_kehadiran_rekap) 
                                        {{$item->ket_kehadiran_rekap}}
                                    @else
                                        {{ $item->ket_kehadiran ?? '-' }} <br>
                                        {{ $item->ket_kehadiran_rekap ?? '-' }} 
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @empty
        <div class="text-center py-4 text-gray-500">
            Data tidak ditemukan.
        </div>
    @endforelse
</div>
</div>