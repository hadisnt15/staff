<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Attendance;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public bool $showAttendanceModal = false;
    public $attendanceDetails = [];

    #[On('open-attendance-detail-modal')]
    public function openModal($userId, $tanggal)
    {
        $this->attendanceDetails = Attendance::query()
            ->where('user_id', $userId)
            ->whereDate('attendance_datetime', $tanggal)
            ->orderBy('attendance_datetime')
            ->get()
            ->map(function ($attendance) {
                $attendance->address = $this->getAddress(
                    $attendance->attendance_lat, $attendance->attendance_lng
                );
                return $attendance;
            });;

        $this->showAttendanceModal = true;
    }

    protected function getAddress($lat, $lng)
    {
        if (!$lat || !$lng) {
            return '-';
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Attendance System'
            ])
            ->timeout(10)
            ->get(
                'https://nominatim.openstreetmap.org/reverse',
                [
                    'lat' => $lat,
                    'lon' => $lng,
                    'format' => 'jsonv2'
                ]
            );

            if ($response->successful()) {
                return $response->json()['display_name'] ?? '-';
            }

            return 'HTTP Error: '.$response->status();

        } catch (\Exception $e) {
            return 'Error: '.$e->getMessage();
        }
    }

    public function cancel()
    {
        $this->showAttendanceModal = false;
    }
};
?>

<div>
    @if($showAttendanceModal)
        <div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between border-b border-white px-2 py-2 bg-primary rounded-t-xl ">
                    <h2 class="text-md text-white font-semibold"><i class="ri-id-card-fill me-2"></i>Detail Kehadiran</h2>
                    <div>
                        <button wire:click="cancel">
                            <span class="bg-danger text-white text-sm font-bold px-1.5 py-1.5 rounded">
                                <i wire:loading.remove wire:target="cancel" class="ri-close-large-fill"></i>
                                <i wire:loading wire:target="cancel" class="ri-loader-4-line animate-spin"></i>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="p-5 overflow-y-auto">
                    <div class="space-y-1">
                        <span class="px-2 font-semibold text-emerald-700">
                            {{ \Carbon\Carbon::parse($attendanceDetails->first()->attendance_datetime)->locale('id')->translatedFormat('l, d F Y') }}
                        </span>
                        @foreach($attendanceDetails as $attendance)
                            <div class="border border-emerald-200 rounded-xl px-2 py-1 bg-white shadow-sm">
                                <div class="flex gap-4">
                                    <!-- Foto -->
                                    <div class="w-24 h-24 flex-shrink-0">
                                        @if($attendance->attendance_photo)
                                            <img src="{{ asset('storage/' . $attendance->attendance_photo) }}" class="w-full h-full object-cover rounded-lg border" alt="Foto Absensi">
                                        @else
                                            <div class="w-full h-full rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">Tidak ada foto</div>
                                        @endif
                                    </div>
                                    <!-- Detail -->
                                    <div class="flex-1 text-xs md:text-sm">
                                        <div class="font-semibold text-emerald-700">
                                            <span class="px-2 py-1 text-xs text-emerald-700">
                                                @if($attendance->attendance_break == 0 && $attendance->attendance_permission == 0)  
                                                    @if($attendance->attendance_type == 'absen_masuk') Datang @else Pulang @endif
                                                @elseif($attendance->attendance_break == 1 && $attendance->attendance_permission == 0)
                                                    @if($attendance->attendance_type == 'absen_masuk') Selesai Istirahat @else Mulai Istirahat @endif
                                                @elseif($attendance->attendance_break == 0 && $attendance->attendance_permission == 1)
                                                    @if($attendance->attendance_type == 'absen_masuk') Selesai Izin Keluar @else Mulai Izin Keluar @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-gray-700 mt-1">
                                            <i class="ri-time-line"></i>{{ \Carbon\Carbon::parse($attendance->attendance_datetime)->format('H:i:s') }}
                                        </div>
                                        <div class="mt-2 text-gray-600">
                                            <div>
                                                <i class="ri-map-pin-line"></i>{{ $attendance->address ?? '-' }} 
                                                <span class="text-xs text-gray-400">({{ $attendance->attendance_lat }}, {{ $attendance->attendance_lng }})</span>
                                            </div>
                                        </div>
                                        @if($attendance->attendance_note)
                                            <div class="mt-2 text-gray-600">
                                                <i class="ri-message-3-line"></i>{{ $attendance->attendance_note }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- Maps -->
                                @if($attendance->attendance_lat && $attendance->attendance_lng)
                                    <a href="https://www.google.com/maps?q={{ $attendance->attendance_lat }},{{ $attendance->attendance_lng }}" target="_blank" class="mt-3 block text-center py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm">
                                        <i class="ri-map-pin-line"></i>Buka Google Maps
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>