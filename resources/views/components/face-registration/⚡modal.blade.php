<?php

use Livewire\Component;
use App\Models\FaceRegistration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

new class extends Component
{
    public bool $showModal = false;

    public $photoBase64;

    public $photos = [
        'front' => null,
        'left'  => null,
        'right' => null,
        'up'    => null,
        'down'  => null,
    ];
    public $labels = [
        'front' => 'Depan',
        'left'  => 'Kiri',
        'right' => 'Kanan',
        'up'    => 'Atas',
        'down'  => 'Bawah',
    ];
    public $step = 'front';
    private $steps = ['front','left','right','up','down'];
    public $existingFace = null;
    public $faceIncomplete = false;

    #[On('open-face-reg-modal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->dispatch('start-camera');
    }

    public function selectPhoto($name)
    {
        $this->step = $name;

        // $this->dispatch('reset-camera');
    }

    public function nextStep()
    {
        // HARUS ada foto dulu
        if (!$this->photos[$this->step]) {
            $this->dispatch('show-alert', message: 'Ambil foto dulu sebelum lanjut');
            return;
        }

        $index = array_search($this->step, $this->steps);

        if ($index === false) return;

        if ($index < count($this->steps) - 1) {
            $this->step = $this->steps[$index + 1];
            // $this->dispatch('reset-camera');
            return;
        }

        // sudah di tahap terakhir
        $this->dispatch(
            'show-alert',
            message: 'Semua foto sudah lengkap. Silakan klik Simpan.'
        );
    }

    public function isLastStep()
    {
        return $this->step === 'down';
    }

    public function takeSnapshot($photoBase64)
    {
        if (!$photoBase64) return;

        $this->photos[$this->step] = $photoBase64;
    }

    public function retakeSnapshot()
    {
        $this->photos[$this->step] = null;

        $this->dispatch('show-alert', message: 'Silakan Ambil Ulang Foto ' . $this->labels[$this->step]);
    }

    public function save()
    {
        if (array_filter($this->photos) !== $this->photos) {
            session()->flash('error', 'Semua foto harus diambil dulu');
            return;
        }

        if ($this->existingFace && $this->existingFace->isLocked()) {
            session()->flash('error', 'Face sudah di-approve dan tidak bisa diubah');
            return;
        }

        $userId = auth()->id();

        $folder = "face-registration/{$userId}";

        Storage::disk('public')->makeDirectory($folder);

        $map = [
            'front' => '1-front',
            'left'  => '2-left',
            'right' => '3-right',
            'up'    => '4-up',
            'down'  => '5-down',
        ];

        // =========================
        // 1. SAVE FILES
        // =========================
        $paths = [];
        foreach ($this->photos as $step => $photo) {
            if (!$photo) {
                continue;
            }
            // Kalau masih URL (foto lama), jangan disimpan ulang
            if (!str_starts_with($photo, 'data:image')) {
                $paths[$step] = $this->existingFace->{$step . '_path'};
                continue;
            }
            $image = str_replace('data:image/jpeg;base64,', '', $photo);
            $image = str_replace(' ', '+', $image);
            $fileName = $map[$step] . '.jpg';
            $path = "{$folder}/{$fileName}";
            Storage::disk('public')->put(
                $path,
                base64_decode($image)
            );
            $paths[$step] = $path;
        }

        // =========================
        // 2. SAVE / UPDATE DB
        // =========================
        $face = FaceRegistration::updateOrCreate(
            ['user_id' => $userId],
            [
                'front_path' => $paths['front'] ?? null,
                'left_path'  => $paths['left'] ?? null,
                'right_path' => $paths['right'] ?? null,
                'up_path'    => $paths['up'] ?? null,
                'down_path'  => $paths['down'] ?? null,
                'status'     => 'pending',
            ]
        );

        $this->loadFace();
        $this->showModal = false;
        $this->dispatch('face-reg-saved', message: 'Registrasi wajah berhasil disimpan.');
    }

    public function loadFace()
    {
        $this->existingFace = auth()->user()->faceRegistration()->first();

        if ($this->existingFace) {
            $this->photos = [
                'front' => $this->existingFace->front_path ? asset('storage/' . $this->existingFace->front_path) : null,
                'left'  => $this->existingFace->left_path ? asset('storage/' . $this->existingFace->left_path) : null,
                'right' => $this->existingFace->right_path ? asset('storage/' . $this->existingFace->right_path) : null,
                'up'    => $this->existingFace->up_path ? asset('storage/' . $this->existingFace->up_path) : null,
                'down'  => $this->existingFace->down_path ? asset('storage/' . $this->existingFace->down_path) : null,
            ];

            $this->faceIncomplete = empty($this->existingFace->front_path) || empty($this->existingFace->left_path) || empty($this->existingFace->right_path) || empty($this->existingFace->up_path) || empty($this->existingFace->down_path);
        }
    }

    public function mount()
    {
        $this->loadFace();
    }

    public function cancel()
    {
        $this->showModal = false;
        $this->dispatch('reset-camera');
    }
};
?>

<div>
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-2">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-md">
                <div class="bg-primary rounded-t-2xl mb-4">
                    <h2 class="text-lg font-bold text-secondary p-2 text-white flex justify-between">
                        <div>
                            <i class="ri-qr-scan-ai-line"></i> Registrasi Wajah
                        </div>
                        <div>
                            <button wire:click="cancel">
                                <span class="bg-danger text-white text-sm font-bold px-1.5 py-1.5 rounded">
                                    <i wire:loading.remove wire:target="cancel" class="ri-close-large-fill"></i>
                                    <i wire:loading wire:target="cancel" class="ri-loader-4-line animate-spin"></i>
                                </span>
                            </button>
                        </div>
                    </h2>
                </div>
                
                <div class="text-center mb-3">
                    @switch($step)
                        @case('front')
                            <h3 class="font-bold text-md">Tahap 1/5</h3>
                            <p class="text-sm -mt-1">Hadapkan wajah lurus ke kamera</p>
                        @break
                        @case('left')
                            <h3 class="font-bold text-md">Tahap 2/5</h3>
                            <p class="text-sm -mt-1">Putar kepala ke kiri</p>
                        @break
                        @case('right')
                            <h3 class="font-bold text-md">Tahap 3/5</h3>
                            <p class="text-sm -mt-1">Putar kepala ke kanan</p>
                        @break
                        @case('up')
                            <h3 class="font-bold text-md">Tahap 4/5</h3>
                            <p class="text-sm -mt-1">Angkat dagu sedikit ke atas</p>
                        @break
                        @case('down')
                            <h3 class="font-bold text-md">Tahap 5/5</h3>
                            <p class="text-sm -mt-1">Tundukkan kepala sedikit ke bawah</p>
                        @break
                    @endswitch
                </div>
                <div wire:ignore class="px-2 py-1">
                    <div id="cam" class="w-full aspect-video rounded-xl overflow-hidden border border-emerald-500"></div>
                </div>
                @error('photo') 
                    <span class="text-red-500 text-sm px-2">{{ $message }}</span> 
                @enderror
                
                <div x-data="{ preview: null }" class="relative">
                    <div class="grid grid-cols-5 gap-2 mb-3 px-2 py-2">
                        @foreach($photos as $name => $photo)
                            <div wire:key="photo-{{ $name }}" wire:click="selectPhoto('{{ $name }}')" @if($photo) @click="preview='{{ $photo }}'" @endif class="cursor-pointer h-14 rounded border flex items-center justify-center overflow-hidden transition {{ $step === $name ? 'border-emerald-600 ring-2 ring-emerald-300' : 'border-gray-300 hover:border-emerald-400' }}">
                                @if($photo)
                                    <img src="{{ $photo }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs text-gray-400 uppercase">
                                        {{ $labels[$name] }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Popup Preview -->
                    <div x-show="preview" x-transition @click.self="preview = null" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-xl p-2 shadow-lg">
                            <img :src="preview" class="w-72 max-w-[90vw] rounded-lg">
                            <button @click="preview = null" class="mt-2 w-full bg-emerald-600 text-white rounded-lg py-1">Tutup</button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 p-2 mt-4">
                    {{-- Ambil Gambar --}}
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="takeSnapshot()" wire:loading.attr="disabled" wire:target="takeSnapshot" @disabled($existingFace && $existingFace->isLocked()) title="{{ $existingFace && $existingFace->isLocked() ? 'Registrasi Wajah Sudah Disetujui' : '' }}" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl h-10 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="takeSnapshot">
                                <i class="ri-camera-4-fill text-3xl"></i>
                            </span>
                            <span wire:loading wire:target="takeSnapshot">
                                <div class="px-2 py-px ring-1 ring-inset ring-brand-subtle text-emerald-900 text-xs font-medium rounded-sm bg-brand-softer animate-pulse">Memuat...</div>
                            </span>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">
                            Ambil Gambar
                        </span>
                    </div>

                    {{-- Ulangi Gambar --}}
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="retakeSnapshot()" wire:loading.attr="disabled" wire:target="retakeSnapshot" @disabled($existingFace && $existingFace->isLocked()) title="{{ $existingFace && $existingFace->isLocked() ? 'Registrasi Wajah Sudah Disetujui' : '' }}" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl h-10 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="retakeSnapshot">
                                <i class="ri-camera-ai-2-fill text-3xl"></i>
                            </span>
                            <span wire:loading wire:target="retakeSnapshot">
                                <div class="px-2 py-px ring-1 ring-inset ring-brand-subtle text-emerald-900 text-xs font-medium rounded-sm bg-brand-softer animate-pulse">Memuat...</div>
                            </span>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">
                            Ulangi Gambar
                        </span>
                    </div>

                    {{-- Tahap Selanjutnya --}}
                    <div class="flex flex-col items-center justify-center text-center">
                        <button type="button" onclick="nextStep()" wire:loading.attr="disabled" wire:target="nextStep" @disabled($existingFace && $existingFace->isLocked() || $this->isLastStep()) title="{{ $this->isLastStep() ? 'Registrasi Wajah Sudah Di Tahap Akhir' : '' }}" title="{{ $existingFace && $existingFace->isLocked() ? 'Registrasi Wajah Sudah Disetujui' : '' }}" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl h-10 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="nextStep">
                                <i class="ri-arrow-right-circle-fill text-3xl"></i>
                            </span>
                            <span wire:loading wire:target="nextStep">
                                <div class="px-2 py-px ring-1 ring-inset ring-brand-subtle text-emerald-900 text-xs font-medium rounded-sm bg-brand-softer animate-pulse">Memuat...</div>
                            </span>
                        </button>
                        <span class="text-xs md:text-sm md:font-semibold text-emerald-900">
                            Tahap Selanjutnya
                        </span>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center text-center px-2 py-2">
                    <button  wire:click="save" wire:loading.attr="disabled" wire:target="save" @disabled($existingFace && $existingFace->isLocked()) title="{{ $existingFace && $existingFace->isLocked() ? 'Registrasi Wajah Sudah Disetujui' : '' }}" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10 disabled:opacity-50 disabled:cursor-not-allowed">
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
                    @if($existingFace && $existingFace->isLocked())
                        <div class="text-red-500 text-sm">
                            Pendaftaran Wajah Sudah Disetujui
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif
</div>

<script>
    let currentCamera = 'user';
    document.addEventListener('livewire:init', () => {
        startCamera();
        Livewire.on('reset-camera', async () => {
            Webcam.reset();

            setTimeout(() => {
                startCamera();
            }, 600);
        });
    });

    document.addEventListener('livewire:init', () => {
        Livewire.on('start-camera', () => {
            setTimeout(() => {
                startCamera();
            }, 100);
        });
    });

    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert(
                'Browser tidak mendukung kamera. Silakan gunakan Google Chrome.'
            );
            return;
        }
        const cam = document.getElementById('cam');

        if (!cam) return;

        if (Webcam) Webcam.reset();

        Webcam.set({
            width: cam.clientWidth,
            height: cam.clientWidth * 0.55,
            image_format: 'jpeg',
            jpeg_quality: 90,
            constraints: {
                facingMode: 'user'
            }
        });

        Webcam.attach('#cam');

        setTimeout(() => {
            const video = document.querySelector('#cam video');
            if (video) {
                video.classList.add('w-full','h-full','object-cover');
            }
        }, 500);
    }

    function capture() {
        const video = document.querySelector('#cam video');

        if (!video || video.videoWidth === 0) {
            alert('Kamera masih loading...');
            return null;
        }

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        return canvas.toDataURL('image/jpeg', 0.9);
    }

    function takeSnapshot() {
        const image = capture();
        if (!image) return;

        @this.call('takeSnapshot', image);
    }

    function retakeSnapshot() {
        @this.call('retakeSnapshot');
    }

    function nextStep() {
        @this.call('nextStep');
    }

    window.addEventListener('show-alert', event => {
        alert(event.detail.message);
    });
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
