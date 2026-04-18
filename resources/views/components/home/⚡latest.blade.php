<?php

use Livewire\Component;

use App\Services\AttendanceSummaryService;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Computed]
    public function attendances()
    {
        // return AttendanceData::collect(AttendanceService::getAttendances(auth()->id()));
        return AttendanceSummaryService::baseQuery(
            auth()->id(),
            now()->format('Y-m')
        )->orderByDesc('tanggal')->paginate(7);
    }
};
?>

<div>
    <div class="mt-2 md:mt-4">
        <div class="text-center font-bold text-emerald-800 text-sm p-1">
            Presensi Satu Minggu Terakhir
        </div>
        {{-- desktop --}}
        <div class="md:block hidden relative overflow-x-auto border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl shadow-xs">
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="text-sm text-body border-b rounded-base border-default">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">
                            Tanggal
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            Kerja
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            Istirahat
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            Izin
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            Keterangan
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->attendances as $item)
                        <tr class="border-b border-default">
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                {{ $item->tanggal }} <br>
                                {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('l') }} ({{ $item->tanggal_merah }})
                            </th>
                            <td class="px-6 py-4">
                                {{ $item->jam_datang ?? '-' }} s/d {{ $item->jam_pulang ?? '-' }} 
                            </td>
                            <td class="px-6 py-4">
                                {{ $item->jam_mulai_istirahat ?? '-' }} s/d {{ $item->jam_selesai_istirahat ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                    
                            </td>
                            <td class="px-6 py-4">
                                {{ $item->ket_kehadiran }} 
                            </td>
                        </tr>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center p-2 text-sm text-red-600 font-semibold">
                                Data Kehadiran Tidak Ditemukan.
                            </div>
                        </div>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- mobile --}}
        <div class="md:hidden block relative overflow-x-auto border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl md:py-4 py-2 shadow-xs rounded-xl">
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <tbody>
                    @forelse ($this->attendances as $item)
                        <tr class="border-b border-emerald-600">
                            <th scope="row" class="px-2 py-4 font-medium text-heading whitespace-nowrap">
                                {{ $item->tanggal }}, {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('l') }} ({{ $item->tanggal_merah }}) <br>
                                <div>
                                    Waktu Kerja: {{ $item->jam_datang ?? '-' }} s/d {{ $item->jam_pulang ?? '-' }} <br>
                                    Istirahat: {{ $item->jam_mulai_istirahat ?? '-' }} s/d {{ $item->jam_selesai_istirahat ?? '-' }} 
                                </div>
                                <div>
                                    Ket: {{ $item->ket_kehadiran }} 
                                </div>
                            </th>
                        </tr>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center p-2 text-sm text-red-600 font-semibold">
                                Data Kehadiran Tidak Ditemukan.
                            </div>
                        </div>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>