<?php

use Livewire\Component;
use App\Models\FaceRegistration;
use Carbon\Carbon;

new class extends Component
{
    public bool $faceIncomplete = false;
    public bool $facePending = false;
    public bool $faceRejected = false;
    public bool $faceApproved = false;

    public function mount()
    {
        $this->existingFace = FaceRegistration::where('user_id', auth()->id())->first();
        $this->faceIncomplete = false;
        $this->facePending = false;
        $this->faceRejected = false;
        $this->faceApproved = false;

        // Belum pernah registrasi
        if (!$this->existingFace) {
            $this->faceIncomplete = true;
            return;
        }

        // Incomplete
        $this->faceIncomplete =
            empty($this->existingFace->front_path) ||
            empty($this->existingFace->left_path) ||
            empty($this->existingFace->right_path) ||
            empty($this->existingFace->up_path) ||
            empty($this->existingFace->down_path);

        // Pending
        if ($this->existingFace->status == 'pending') {
            $this->facePending = true;
        }

        // Reject
        if ($this->existingFace->status == 'rejected') {
            $this->faceRejected = true;
        }

        // Approve (24 jam)
        if ($this->existingFace->status == 'approved' && $this->existingFace->approved_at && Carbon::parse($this->existingFace->approved_at)->addHours(24)->isFuture()) {
            $this->faceApproved = true;
        }
    }
};
?>

<div>
    @if($faceIncomplete)
        <div class="p-2">
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex gap-3">
                    <i class="ri-error-warning-fill text-3xl text-amber-500"></i>
                    <div>
                        <p class="font-bold text-amber-800">Registrasi wajah belum lengkap</p>
                        <p class="text-sm text-amber-700">Lengkapi 5 foto wajah agar dapat melakukan absensi.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if($facePending)
        <div class="p-2">
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                <div class="flex gap-3">
                    <i class="ri-time-fill text-3xl text-sky-500"></i>
                    <div>
                        <p class="font-bold text-sky-800">Menunggu Persetujuan</p>
                        <p class="text-sm text-sky-700">Registrasi wajah sedang diperiksa admin.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if($faceRejected)
        <div class="p-2">
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
                <div class="flex gap-3">
                    <i class="ri-close-circle-fill text-3xl text-red-500"></i>
                    <div>
                        <p class="font-bold text-red-800">Registrasi Wajah Ditolak</p>
                        <p class="text-sm text-red-700">Silakan lakukan registrasi ulang dengan foto yang lebih jelas.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if($faceApproved)
        <div class="p-2">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex gap-3">
                    <i class="ri-checkbox-circle-fill text-3xl text-emerald-500"></i>
                    <div>
                        <p class="font-bold text-emerald-800">Registrasi Wajah Disetujui</p>
                        <p class="text-sm text-emerald-700">Anda sekarang sudah dapat melakukan absensi.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>