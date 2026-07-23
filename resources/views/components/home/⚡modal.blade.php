<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Attendance;

new class extends Component
{
    use WithFileUploads;

    public $note;
    public $photoBase64;
    public $lat;
    public $lng;
    public $type;
    public $mode = 'normal';
    public $lockedMode = null;
    public $disableSpecialMode = false;
    public $showModalCheckin = false;
    public $showModalCheckout = false;
    public $showModalBusinessTrip = false;
    public $showModalLeave = false;
    public $photoUploaded = false;
    // public $officeLat = -3.3579102791671946;
    // public $officeLng = 114.63293744171641;
    public $officeLat;
    public $officeLng;
    public $allowedRadius = 30; // meter
    public $insideRadius = false;
    public $breakUsed = false;
    public $permissionUsed = false;

    protected $rules = [
        // 'photo' => 'required|image|max:2048',
        'lat' => 'required',
        'lng' => 'required',
    ];

    protected $listeners = [
        'openCheckinModal' => 'openModalCheckin',
        'openCheckoutModal' => 'openModalCheckout',
        'openBusinessTripModal' => 'openModalBusinessTrip',
        'openLeaveModal' => 'openModalLeave',
        'setRadiusStatus',
    ];

    public function openModalCheckin()
    {
        $this->showModalCheckin = true;
        $this->type = 'absen_masuk';
        $this->dispatch('init-gps');
    }
    
    public function openModalCheckout()
    {
        $this->showModalCheckout = true;
        $this->type = 'absen_keluar';
        $this->dispatch('init-gps');
    }
    
    public function openModalBusinessTrip()
    {
        $this->showModalBusinessTrip = true;
        $this->type = 'luar_kota';
        $this->dispatch('init-gps');
    }
    
    public function openModalLeave()
    {
        $this->showModalLeave = true;
        $this->type = 'tidak_hadir';
        $this->dispatch('init-gps');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) *
            sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function setRadiusStatus($status)
    {
        $this->insideRadius = $status;
    }

    public function mount()
    {
        $branch = auth()->user()->branch;

        $this->officeLat = (float) $branch->lat;
        $this->officeLng = (float) $branch->lng;

        $userId = auth()->id();

        $last = Attendance::where('user_id', $userId)->whereDate('attendance_datetime', today())->latest('attendance_datetime')->first();

        // ✅ cek sudah absen masuk hari ini atau belum
        $hasCheckinToday = Attendance::where('user_id', $userId)
            ->whereDate('attendance_datetime', today())
            ->where('attendance_type', 'absen_masuk')
            ->where('attendance_break', 0)
            ->where('attendance_permission', 0)
            ->exists();

        // ❗ kalau belum ada absen masuk → disable break & permission
        if (!$hasCheckinToday) {
            $this->disableSpecialMode = true;
        } else {
            $this->disableSpecialMode = false;
        }

        if (!$last) {
            $this->mode = 'normal';
            return;
        }

        // jika terakhir break dan belum ditutup
        if ($last->attendance_break == 1 && $last->attendance_type == 'absen_keluar') {
            $this->mode = 'break';
            $this->lockedMode = 'break';
        }
        // jika terakhir permission dan belum ditutup
        elseif ($last->attendance_permission == 1 && $last->attendance_type == 'absen_keluar') {
            $this->mode = 'permission';
            $this->lockedMode = 'permission';
        } else {
            $this->mode = 'normal';
            $this->lockedMode = null;
        }

        $this->breakUsed = Attendance::where('user_id', $userId)
            ->whereDate('attendance_datetime', today())
            ->where('attendance_break', 1)
            ->where('attendance_type', 'absen_keluar')
            ->exists();

        $this->permissionUsed = Attendance::where('user_id', $userId)
            ->whereDate('attendance_datetime', today())
            ->where('attendance_permission', 1)
            ->where('attendance_type', 'absen_keluar')
            ->exists();
    }

    public function clearPhotoError()
    {
        $this->resetErrorBag('photo');
    }

    public function save()
    {
        if (!$this->photoBase64) {
            $this->addError('photo', 'Foto wajib diambil');
            return;
        }
        
        $this->validate();
        if (
            in_array($this->type, ['absen_masuk', 'absen_keluar']) &&
            $this->mode === 'permission' &&
            blank($this->note)
        ) {
            $this->addError('note', 'Keterangan wajib diisi saat melakukan izin.');
            return;
        }

        if (
            in_array($this->type, ['luar_kota', 'tidak_hadir']) &&
            blank($this->note)
        ) {
            $this->addError('note', 'Keterangan wajib diisi.');
            return;
        }

        $distance = $this->calculateDistance(
            $this->lat,
            $this->lng,
            $this->officeLat,
            $this->officeLng
        );

        if ($distance > $this->allowedRadius) {
            $this->addError(
                'lat',
                'Anda berada di luar area absensi. Jarak Anda ' .
                round($distance) .
                ' meter dari lokasi kantor.'
            );
            return;
        }

        $userId = auth()->id();

        // 🔥 CONVERT BASE64 → FILE
        $image = str_replace('data:image/jpeg;base64,', '', $this->photoBase64);
        $image = str_replace(' ', '+', $image);

        $imageName = sprintf('attendance/%d/%s.jpg', $userId, now()->format('Ymd_His') . '_' . uniqid());

        \Storage::disk('public')->put($imageName, base64_decode($image));

        $photoPath = $imageName;

        
        $break = 0;
        $permission = 0;
        if ($this->mode === 'break') {
            $break = 1;
        }
        if ($this->mode === 'permission') {
            $permission = 1;
        }

        $last = Attendance::where('user_id', $userId)
            ->whereDate('attendance_datetime', today())
            ->latest('attendance_datetime')
            ->first();

        $today = Attendance::where('user_id', $userId)
            ->whereDate('attendance_datetime', today())
            ->get();

        $currentType = $this->type; // absen_masuk / absen_keluar

        $break = $this->mode === 'break' ? 1 : 0;
        $permission = $this->mode === 'permission' ? 1 : 0;

        if ($last) {
            // BREAK belum selesai
            if ($last->attendance_break == 1 && $last->attendance_type == 'absen_keluar') {
                if (!($currentType == 'absen_masuk' && $break == 1)) {
                    throw new \Exception('Harus menyelesaikan istirahat dulu');
                }
            }
            // PERMISSION belum selesai
            if ($last->attendance_permission == 1 && $last->attendance_type == 'absen_keluar') {
                if (!($currentType == 'absen_masuk' && $permission == 1)) {
                    throw new \Exception('Harus kembali dari izin dulu');
                }
            }
        }

        // BREAK
        $breakCount = $today->where('attendance_break', 1)
            ->where('attendance_type', 'absen_keluar')
            ->count();

        if ($break == 1 && $currentType == 'absen_keluar' && $breakCount >= 1) {
            throw new \Exception('Istirahat hanya boleh 1x sehari');
        }

        // PERMISSION
        $permissionCount = $today->where('attendance_permission', 1)
            ->where('attendance_type', 'absen_keluar')
            ->count();

        if ($permission == 1 && $currentType == 'absen_keluar' && $permissionCount >= 1) {
            throw new \Exception('Izin hanya boleh 1x sehari');
        }

        if ($break == 1 && $permission == 1) {
            throw new \Exception('Tidak boleh break dan izin bersamaan');
        }

        $response = Http::timeout(60)
            ->attach(
                'photo',
                fopen(
                    storage_path('app/public/'.$photoPath),
                    'r'
                ),
                basename($photoPath)
            )
            ->post(
                'http://127.0.0.1:5000/verify',
                [
                    'user_id' => auth()->id()
                ]
            );

        $result = $response->json();
        if (!$result['success']) {
            Storage::disk('public')->delete($photoPath);
            $message = $result['message'] ?? 'Wajah tidak cocok';
            if (isset($result['similarity'])) {
                $message .= '. Similarity: '.$result['similarity'];
            }
            $this->addError(
                'photo',
                $message
            );
            return;
        }
        
        Attendance::create([
            'user_id' => auth()->id(),
            'attendance_datetime' => now(auth()->user()->timezone),
            'attendance_break' => $break,
            'attendance_permission' => $permission,
            'attendance_type' => $this->type,
            'attendance_note' => $this->note,
            'attendance_photo' => $photoPath,
            'attendance_status' => 'disetujui',
            'attendance_days_count' => 0,
            'attendance_approved_by' => 1,
            'attendance_lat' => $this->lat,
            'attendance_lng' => $this->lng,
        ]);

        $this->dispatch('attendance-saved');
        $this->reset(['note','photoBase64','lat','lng']);
        $this->showModalCheckin = false;
        $this->showModalCheckout = false;
        $this->showModalBusinessTrip = false;
        $this->showModalLeave = false;
        $this->break = false;
        $this->dispatch('reset-camera');
        $this->mount();

        session()->flash('success', 'Data kehadiran berhasil dicatat!');
    }

    public function cancel()
    {
        $this->reset(['note', 'lat', 'lng', 'type']);
        $this->dispatch('reset-camera');
        $this->showModalCheckin = false;
        $this->showModalCheckout = false;
        $this->showModalBusinessTrip = false;
        $this->showModalLeave = false;
        $this->break = false;
    }
};
?>

<div>
    @if($showModalCheckin)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-2">
            <div class="bg-white w-full max-w-md rounded-2xl border">
                <div class="bg-primary rounded-t-2xl shadow-md mb-4">
                    <h2 class="text-lg font-bold text-secondary p-2 text-white flex justify-between">
                        <div>
                            <i class="ri-login-circle-fill"></i> Masuk
                        </div>
                        <div>
                            <span wire:ignore class="bg-success-soft text-fg-success-strong text-sm font-bold px-1.5 py-1.5 rounded border border-emerald-200" id="clock"></span>
                            <button wire:click="cancel">
                                <span class="bg-danger text-white text-sm font-bold px-1.5 py-1.5 rounded">
                                    <i wire:loading.remove wire:target="cancel" class="ri-close-large-fill"></i>
                                    <i wire:loading wire:target="cancel" class="ri-loader-4-line animate-spin"></i>
                                </span>
                            </button>
                        </div>
                    </h2>
                </div>
                <div wire:ignore class="px-2 py-1">
                    <div id="cam" class="w-full aspect-video rounded-xl overflow-hidden  border border-emerald-500"></div>
                </div>
                @error('photo') 
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span> 
                @enderror
                <div class="px-2 py-1">
                    <div wire:ignore id="map" class="h-[150px] w-full rounded-xl  border border-emerald-500"></div>
                </div>
                <div class="px-2 py-1">
                    <textarea wire:model="note" class="w-full  border border-emerald-500 rounded-xl" placeholder="Catatan (opsional)"></textarea>
                </div>
                @error('note')
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span>
                @enderror
                <ul class="grid grid-cols-3 gap-2 px-2 py-2">
                    <li>
                        <input wire:model="mode" type="radio" id="mode-normal" value="normal" class="hidden peer" {{ $lockedMode ? 'disabled' : '' }}>
                        <label for="mode-normal" class="flex items-center justify-center rounded-lg border px-1 py-1 text-xs font-medium cursor-pointer border-gray-300 bg-white text-gray-700 hover:bg-gray-50 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                            Datang
                        </label>
                    </li>
                    <li>
                        <input wire:model="mode" type="radio" id="mode-break" value="break" class="hidden peer" {{ ($lockedMode && $lockedMode !== 'break') || ($disableSpecialMode && !$lockedMode) ? 'disabled' : '' }}>
                        <label for="mode-break" class="flex items-center justify-center rounded-lg border px-1 py-1 text-xs font-medium cursor-pointer border-gray-300 bg-white text-gray-700
                                hover:bg-gray-50 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                            Selesai Istirahat
                        </label>
                    </li>
                    <li>
                        <input wire:model="mode" type="radio" id="mode-permission" value="permission" class="hidden peer" {{ ($lockedMode && $lockedMode !== 'permission') || ($disableSpecialMode && !$lockedMode) ? 'disabled' : '' }}>
                        <label for="mode-permission" class="flex items-center justify-center rounded-lg border px-1 py-1 text-xs font-medium cursor-pointer border-gray-300 bg-white text-gray-700 hover:bg-gray-50 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                            Selesai Izin
                        </label>
                    </li>
                </ul>
                @if($breakUsed || $permissionUsed)
                    <div class="mt-2 mx-2 rounded-lg border border-amber-200 bg-amber-50 px-2 py-1 text-xs text-amber-700">
                        <i class="ri-information-line"></i>
                        @if($breakUsed && $permissionUsed)
                            Jatah istirahat dan izin keluar hari ini sudah digunakan.
                        @elseif($breakUsed)
                            Jatah istirahat hari ini sudah digunakan.
                        @else
                            Jatah izin keluar hari ini sudah digunakan.
                        @endif
                    </div>
                @endif
                <div>
                    <input type="hidden" wire:model="lat">
                    @error('lat') 
                        <span class="text-red-500 text-sm" readonly>{{ $message }}</span> 
                    @enderror
                    <input type="hidden" wire:model="lng">
                    @error('lng') 
                        <span class="text-red-500 text-sm" readonly>{{ $message }}</span> 
                    @enderror
                </div>
                <div class="grid grid-cols-3 gap-2 p-2 mt-4">
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="takeSnapshot()" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10">
                            <i class="ri-camera-4-fill text-3xl"></i>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Ambil Gambar</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="retakeSnapshot()" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10">
                            <i class="ri-camera-switch-fill text-3xl"></i>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Ulangi Gambar</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button wire:click="save" @disabled(!$insideRadius) wire:loading.attr="disabled" wire:target="save" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10 disabled:opacity-50 disabled:cursor-not-allowed">
                            <!-- icon normal -->
                            <span wire:loading.remove wire:target="save">
                                <i class="ri-arrow-down-circle-fill text-3xl"></i>
                            </span>
                            <!-- spinner -->
                            <div wire:loading.delay wire:target="save" class="flex items-center justify-center bg-neutral-secondary-soft p-1 border border-default text-fg-brand-strong text-xs font-medium rounded-base">
                                <div class="px-2 py-px ring-1 ring-inset ring-brand-subtle text-emerald-900 text-xs font-medium rounded-sm bg-brand-softer animate-pulse">Proses...</div>
                            </div>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Simpan</span>
                    </div>
                </div>
            </div>
        </div>
    @elseif($showModalCheckout)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-2">
            <div class="bg-white w-full max-w-md rounded-2xl border">
                <div class="bg-primary rounded-t-2xl mb-4">
                    <h2 class="text-lg font-bold text-secondary p-2 text-white flex justify-between">
                        <div>
                            <i class="ri-logout-circle-fill"></i> Keluar 
                        </div>
                        <div>
                            <span wire:ignore class="bg-success-soft text-fg-success-strong text-sm font-bold px-1.5 py-1.5 rounded border border-emerald-200" id="clock"></span>
                            <button wire:click="cancel">
                                <span class="bg-danger text-white text-sm font-bold px-1.5 py-1.5 rounded">
                                    <i wire:loading.remove wire:target="cancel" class="ri-close-large-fill"></i>
                                    <i wire:loading wire:target="cancel" class="ri-loader-4-line animate-spin"></i>
                                </span>
                            </button>
                        </div>
                    </h2>
                </div>
                <div wire:ignore class="px-2 py-1">
                    <div id="cam" class="w-full aspect-video rounded-xl overflow-hidden  border border-emerald-500"></div>
                </div>
                @error('photo') 
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span> 
                @enderror
                <div class="px-2 py-1">
                    <div wire:ignore id="map" class="h-[150px] w-full rounded-xl  border border-emerald-500"></div>
                </div>
                <div class="px-2 py-1">
                    <textarea wire:model="note" class="w-full  border border-emerald-500 rounded-xl" placeholder="Catatan (opsional)"></textarea>
                </div>
                @error('note')
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span>
                @enderror
                {{-- <div class="flex flex-wrap gap-4 px-2 py-2 text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="mode" class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500" value="normal" {{ $lockedMode ? 'disabled' : '' }}>
                        Pulang
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="mode" class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500" value="break" {{ ($lockedMode && $lockedMode !== 'break') || ($disableSpecialMode && !$lockedMode) || ($breakUsed && !$lockedMode) ? 'disabled' : '' }}>
                        Mulai Istirahat
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="mode" class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500" value="permission" {{ ($lockedMode && $lockedMode !== 'permission') || ($disableSpecialMode && !$lockedMode) || ($permissionUsed && !$lockedMode) ? 'disabled' : '' }}>
                        Mulai Izin Keluar
                    </label>
                </div> --}}
                <ul class="grid grid-cols-3 gap-2 px-2 py-2">
                    <li>
                        <input wire:model="mode" type="radio" id="mode-normal" value="normal" class="hidden peer" {{ $lockedMode ? 'disabled' : '' }}>
                        <label for="mode-normal" class="flex items-center justify-center rounded-lg border px-1 py-1 text-xs font-medium cursor-pointer border-gray-300 bg-white text-gray-700 hover:bg-gray-50 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                            Pulang
                        </label>
                    </li>
                    <li>
                        <input wire:model="mode" type="radio" id="mode-break" value="break" class="hidden peer" {{ ($lockedMode && $lockedMode !== 'break') || ($disableSpecialMode && !$lockedMode) || ($breakUsed && !$lockedMode) ? 'disabled' : '' }}>
                        <label for="mode-break" class="flex items-center justify-center rounded-lg border px-1 py-1 text-xs font-medium cursor-pointer border-gray-300 bg-white text-gray-700
                                hover:bg-gray-50 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                            Mulai Istirahat
                        </label>
                    </li>
                    <li>
                        <input wire:model="mode" type="radio" id="mode-permission" value="permission" class="hidden peer" {{ ($lockedMode && $lockedMode !== 'permission') || ($disableSpecialMode && !$lockedMode) || ($permissionUsed && !$lockedMode) ? 'disabled' : '' }}>
                        <label for="mode-permission" class="flex items-center justify-center rounded-lg border px-1 py-1 text-xs font-medium cursor-pointer border-gray-300 bg-white text-gray-700 hover:bg-gray-50 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                            Mulai Izin
                        </label>
                    </li>
                </ul>
                @if($breakUsed || $permissionUsed)
                    <div class="mt-2 mx-2 rounded-lg border border-amber-200 bg-amber-50 px-2 py-1 text-xs text-amber-700">
                        <i class="ri-information-line"></i>
                        @if($breakUsed && $permissionUsed)
                            Jatah istirahat dan izin keluar hari ini sudah digunakan.
                        @elseif($breakUsed)
                            Jatah istirahat hari ini sudah digunakan.
                        @else
                            Jatah izin keluar hari ini sudah digunakan.
                        @endif
                    </div>
                @endif
                <div>
                    <input type="hidden" wire:model="lat">
                    @error('lat') 
                        <span class="text-red-500 text-sm" readonly>{{ $message }}</span> 
                    @enderror
                    <input type="hidden" wire:model="lng">
                    @error('lng') 
                        <span class="text-red-500 text-sm" readonly>{{ $message }}</span> 
                    @enderror
                </div>
                <div class="grid grid-cols-3 gap-2 p-2 mt-4">
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="takeSnapshot()" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10">
                            <i class="ri-camera-4-fill text-3xl"></i>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Ambil Gambar</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="retakeSnapshot()" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10">
                            <i class="ri-camera-switch-fill text-3xl"></i>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Ulangi Gambar</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button wire:click="save" @disabled(!$insideRadius) wire:loading.attr="disabled" wire:target="save" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10 disabled:opacity-50 disabled:cursor-not-allowed">
                            <!-- icon normal -->
                            <span wire:loading.remove wire:target="save">
                                <i class="ri-arrow-down-circle-fill text-3xl"></i>
                            </span>
                            <!-- spinner -->
                            <div wire:loading.delay wire:target="save" class="flex items-center justify-center bg-neutral-secondary-soft p-1 border border-default text-fg-brand-strong text-xs font-medium rounded-base">
                                <div class="px-2 py-px ring-1 ring-inset ring-brand-subtle text-emerald-900 text-xs font-medium rounded-sm bg-brand-softer animate-pulse">Proses...</div>
                            </div>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Simpan</span>
                    </div>
                </div>
            </div>
        </div>
    @elseif($showModalBusinessTrip)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-2">
            <div class="bg-white w-full max-w-md rounded-2xl border">
                <div class="bg-primary rounded-t-2xl mb-4">
                    <h2 class="text-lg font-bold text-secondary p-2 text-white flex justify-between">
                        <div>
                            <i class="ri-compass-3-fill"></i> Luar Kota
                        </div>
                        <div>
                            <span class="bg-success-soft text-fg-success-strong text-sm font-bold px-1.5 py-1.5 rounded border border-emerald-200" id="clock"></span>
                            <button wire:click="cancel">
                                <span class="bg-danger text-white text-sm font-bold px-1.5 py-1.5 rounded">
                                    <i wire:loading.remove wire:target="cancel" class="ri-close-large-fill"></i>
                                    <i wire:loading wire:target="cancel" class="ri-loader-4-line animate-spin"></i>
                                </span>
                            </button>
                        </div>
                    </h2>
                </div>
                <div wire:ignore class="px-2 py-1">
                    <div id="cam" class="w-full aspect-video rounded-xl overflow-hidden  border border-emerald-500"></div>
                </div>
                @error('photo') 
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span> 
                @enderror
                <div class="px-2 py-1">
                    <div wire:ignore id="map" class="h-[150px] w-full rounded-xl  border border-emerald-500"></div>
                </div>
                <div class="px-2 py-1">
                    <textarea wire:model="note" class="w-full  border border-emerald-500 rounded-xl" placeholder="Catatan (opsional)"></textarea>
                </div>
                @error('note')
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span>
                @enderror
                <div>
                    <input type="hidden" wire:model="lat">
                    @error('lat') 
                        <span class="text-red-500 text-sm" readonly>{{ $message }}</span> 
                    @enderror
                    <input type="hidden" wire:model="lng">
                    @error('lng') 
                        <span class="text-red-500 text-sm" readonly>{{ $message }}</span> 
                    @enderror
                </div>
                <div class="grid grid-cols-3 gap-2 p-2 mt-4">
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="takeSnapshot()" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10">
                            <i class="ri-camera-4-fill text-3xl"></i>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Ambil Gambar</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="retakeSnapshot()" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10">
                            <i class="ri-camera-switch-fill text-3xl"></i>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Ulangi Gambar</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button wire:click="save" @disabled(!$insideRadius) wire:loading.attr="disabled" wire:target="save" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10 disabled:opacity-50 disabled:cursor-not-allowed">
                            <!-- icon normal -->
                            <span wire:loading.remove wire:target="save">
                                <i class="ri-arrow-down-circle-fill text-3xl"></i>
                            </span>
                            <!-- spinner -->
                            <div wire:loading.delay wire:target="save" class="flex items-center justify-center bg-neutral-secondary-soft p-1 border border-default text-fg-brand-strong text-xs font-medium rounded-base">
                                <div class="px-2 py-px ring-1 ring-inset ring-brand-subtle text-emerald-900 text-xs font-medium rounded-sm bg-brand-softer animate-pulse">Proses...</div>
                            </div>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Simpan</span>
                    </div>
                </div>
            </div>
        </div>
    @elseif($showModalLeave)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-2">
            <div class="bg-white w-full max-w-md rounded-2xl border">
                <div class="bg-primary rounded-t-2xl mb-4">
                    <h2 class="text-lg font-bold text-secondary p-2 text-white flex justify-between">
                        <div>
                            <i class="ri-indeterminate-circle-fill"></i> Tidak Hadir
                        </div>
                        <div>
                            <span class="bg-success-soft text-fg-success-strong text-sm font-bold px-1.5 py-1.5 rounded border border-emerald-200" id="clock"></span>
                            <button wire:click="cancel">
                                <span class="bg-danger text-white text-sm font-bold px-1.5 py-1.5 rounded">
                                    <i wire:loading.remove wire:target="cancel" class="ri-close-large-fill"></i>
                                    <i wire:loading wire:target="cancel" class="ri-loader-4-line animate-spin"></i>
                                </span>
                            </button>
                        </div>
                    </h2>
                </div>
                <div wire:ignore class="px-2 py-1">
                    <div id="cam" class="w-full aspect-video rounded-xl overflow-hidden  border border-emerald-500"></div>
                </div>
                @error('photo') 
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span> 
                @enderror
                <div class="px-2 py-1">
                    <div wire:ignore id="map" class="h-[150px] w-full rounded-xl  border border-emerald-500"></div>
                </div>
                <div class="px-2 py-1">
                    <textarea wire:model="note" class="w-full  border border-emerald-500 rounded-xl" placeholder="Catatan (opsional)"></textarea>
                </div>
                @error('note')
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span>
                @enderror
                <div>
                    <input type="hidden" wire:model="lat">
                    @error('lat') 
                        <span class="text-red-500 text-sm" readonly>{{ $message }}</span> 
                    @enderror
                    <input type="hidden" wire:model="lng">
                    @error('lng') 
                        <span class="text-red-500 text-sm" readonly>{{ $message }}</span> 
                    @enderror
                </div>
                <div class="grid grid-cols-3 gap-2 p-2 mt-4">
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="takeSnapshot()" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10">
                            <i class="ri-camera-4-fill text-3xl"></i>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Ambil Gambar</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="retakeSnapshot()" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10">
                            <i class="ri-camera-switch-fill text-3xl"></i>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Ulangi Gambar</span>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button wire:click="save" @disabled(!$insideRadius) wire:loading.attr="disabled" wire:target="save" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10 disabled:opacity-50 disabled:cursor-not-allowed">
                            <!-- icon normal -->
                            <span wire:loading.remove wire:target="save">
                                <i class="ri-arrow-down-circle-fill text-3xl"></i>
                            </span>
                            <!-- spinner -->
                            <div wire:loading.delay wire:target="save" class="flex items-center justify-center bg-neutral-secondary-soft p-1 border border-default text-fg-brand-strong text-xs font-medium rounded-base">
                                <div class="px-2 py-px ring-1 ring-inset ring-brand-subtle text-emerald-900 text-xs font-medium rounded-sm bg-brand-softer animate-pulse">Proses...</div>
                            </div>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">Simpan</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    
</script>
<script>
    let currentCamera = 'user';
    document.addEventListener('livewire:init', () => {
        Livewire.on('init-gps', () => {
            updateClock();
            setInterval(updateClock, 1000);
            initGPS();
            startCamera();
        });
        Livewire.on('reset-camera', () => {
            Webcam.reset();
        });
    });

    function updateClock() {
        // const now = new Date();
        const now = new Date(new Date().toLocaleString("en-US", {
            timeZone: branchTimezone
        }));

        const day = now.getDate();
        const month = now.getMonth() + 1;
        const year = now.getFullYear();

        const hour = String(now.getHours()).padStart(2, '0');
        const minute = String(now.getMinutes()).padStart(2, '0');
        const second = String(now.getSeconds()).padStart(2, '0');

        const formatted = `${day}/${month}/${year} ${hour}:${minute}:${second}`;

        const el = document.getElementById('clock');
        if (el) el.innerText = formatted;
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;

        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) *
            Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c;
    }

    function initGPS() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {

                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    // if (accuracy > 20) {
                    //     alert(
                    //         "GPS belum akurat (" +
                    //         Math.round(accuracy) +
                    //         " meter).\nSilakan tunggu beberapa detik lalu coba lagi."
                    //     );
                    //     return;
                    // }

                    @this.set('lat', lat);
                    @this.set('lng', lng);

                    const map = L.map('map').setView([lat, lng], 17);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);

                    L.marker([lat, lng]).addTo(map);

                    // const officeLat = -3.3579102791671946;
                    // const officeLng = 114.63293744171641;
                    const officeLat = Number(@js($officeLat));
                    const officeLng = Number(@js($officeLng));

                    console.log(officeLat, officeLng, typeof officeLat);

                    const distance = calculateDistance(
                        lat, lng, officeLat, officeLng
                    );

                    if (distance <= 30) {
                        @this.call('setRadiusStatus', true);
                    } else {
                        @this.call('setRadiusStatus', false);

                        alert(
                            'Anda berada di luar area absensi. Jarak: ' +
                            Math.round(distance) +
                            ' meter'
                        );
                    }

                    L.circle([officeLat, officeLng], {
                        color: 'green',
                        fillColor: '#00b4d8',
                        fillOpacity: 0.5,
                        radius: 30
                    }).addTo(map);

                    // L.popup()
                    //     .setLatLng([officeLat, officeLng])
                    //     .setContent("PT Kapuas Kencana Jaya")
                    //     .openOn(map);
                },

                function() {
                    alert("Izinkan akses lokasi untuk absen.");
                },

                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }
    }

    // function startCamera() {
    //     const cam = document.getElementById('cam');
    //     Webcam.reset();
    //     Webcam.set({
    //         width: cam.clientWidth,
    //         height: cam.clientWidth * 0.55,
    //         image_format: 'jpeg',
    //         jpeg_quality: 90,
    //         constraints: {
    //             facingMode: currentCamera
    //         }
    //     });
    //     Webcam.attach('#cam');
    //     setTimeout(() => {
    //         const video = document.querySelector('#cam video');
    //         if (video) {
    //             video.classList.add(
    //                 'w-full',
    //                 'h-full',
    //                 'object-cover'
    //             );
    //         }
    //     }, 300);
    // }
    function startCamera() {
        const cam = document.getElementById('cam');

        Webcam.reset();

        Webcam.set({
            width: cam.clientWidth,
            height: cam.clientWidth * 0.55,
            image_format: 'jpeg',
            jpeg_quality: 90,
            constraints: {
                video: {
                    facingMode: {
                        ideal: 'user'
                    }
                },
                audio: false
            }
        });

        Webcam.attach('#cam');

        Webcam.on('live', function() {
            console.log('CAMERA LIVE');

            const video = document.querySelector('#cam video');

            if (video) {
                video.classList.add(
                    'w-full',
                    'h-full',
                    'object-cover'
                );
                video.play();
            }
        });
    }

    function takeSnapshot() {

        const video = document.querySelector('#cam video');

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const data_uri = canvas.toDataURL('image/jpeg', 0.9);

        document.getElementById('cam').innerHTML =
            `<img src="${data_uri}" class="w-full h-full object-cover rounded-xl">`;

        // 🔥 SET KE LIVEWIRE
        @this.set('photoBase64', data_uri);

        // 🔥 HAPUS ERROR LANGSUNG
        @this.call('clearPhotoError');
    }

    function retakeSnapshot() {
        document.getElementById('cam').innerHTML = '';
        startCamera();

        @this.set('photoBase64', null);
    }

    function switch_camera() {
        currentCamera = (currentCamera === 'user') ? 'environment' : 'user';
        startCamera();
    }

    window.addEventListener('show-alert', event => {
        alert(event.detail.message);
    });
</script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
