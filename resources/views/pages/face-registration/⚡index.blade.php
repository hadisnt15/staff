<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\FaceRegistration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new #[Title('Daftarkan Wajah')] class extends Component
{
    public string $successMessage = '';
    public $existingFace = null;
    public $faceIncomplete = false;

    #[\Livewire\Attributes\On('face-reg-saved')]
    public function showSuccess($message)
    {
        $this->successMessage = $message;
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

    public function openModal()
    {
        $this->dispatch('open-face-reg-modal');
    }
};
?>

<div class="md:p-4 px-4 py-2 mx-auto max-w-7xl">
    <div class="relative overflow-x-auto shadow-md rounded-md border border-gray-200 py-2 px-2 bg-white">
        <nav class=" flex mb-4 px-5 py-3 border rounded-md bg-emerald-50 border-gray-200" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="ms-1 text-sm font-medium text-emerald-800 md:ms-2">
                            Registrasi Wajah
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
        {{-- <button wire:click="$dispatch('open-face-reg-modal')" id="btnLeave" class="text-xs rounded-md px-3 py-2 bg-emerald-800 hover:bg-emerald-500 font-medium text-white"> 
            Registrasikan Wajah
        </button> --}}
        <button wire:click="openModal" class="text-xs rounded-md px-3 py-2 bg-emerald-800 hover:bg-emerald-500 font-medium text-white">
            <i wire:loading wire:target="openModal" class="ri-loader-4-line animate-spin"></i>
            Registrasikan Wajah
        </button>

        @if($successMessage)
            <div class="rounded-lg border border-green-300 bg-green-100 p-3 my-2">
                <div class="font-semibold text-green-800">
                    ✅ Berhasil
                </div>
                <div class="text-sm text-green-700">
                    {{ $successMessage }}
                </div>
            </div>
        @endif
        
        <livewire:face-registration.modal />
        @if($existingFace)
            @if($faceIncomplete)
                <div class="my-3 rounded-lg bg-orange-100 border border-orange-300 p-3">
                    <div class="font-semibold text-orange-800">⚠️ Wajah Belum Lengkap</div>
                    <div class="text-sm text-orange-700">Silakan lengkapi seluruh foto wajah terlebih dahulu.</div>
                </div>
            @else
                @switch($existingFace->status)
                    @case('pending')
                        <div class="my-3 rounded-lg bg-yellow-100 border border-yellow-300 p-3">
                            <div class="font-semibold text-yellow-800">⏳ Menunggu Persetujuan</div>
                            <div class="text-sm text-yellow-700">Foto wajah Anda sedang diperiksa oleh admin.</div>
                        </div>
                    @break
                    @case('approved')
                        <div class="my-3 rounded-lg bg-green-100 border border-green-300 p-3">
                            <div class="font-semibold text-green-800">✅ Disetujui</div>
                            <div class="text-sm text-green-700">Registrasi wajah telah disetujui.</div>
                        </div>
                    @break
                    @case('rejected')
                        <div class="my-3 rounded-lg bg-red-100 border border-red-300 p-3">
                            <div class="font-semibold text-red-800">❌ Ditolak</div>
                            <div class="text-sm text-red-700">Registrasi wajah ditolak. Silakan ambil ulang foto dan kirim kembali.</div>
                        </div>
                    @break
                @endswitch
            @endif
        @endif
    </div>
</div>


