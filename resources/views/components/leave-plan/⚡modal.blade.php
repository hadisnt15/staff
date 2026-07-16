<?php

use Livewire\Component;
use App\Models\LeavePlan;
use App\Models\LeavePlanDate;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

new class extends Component
{
    public bool $showModal = false;
    public $title = '';
    public $note = '';
    // nanti diisi oleh Flatpickr
    public array $leaveDates = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'note' => 'nullable|string',
        'leaveDates' => 'required|array|min:1',
    ];

    #[On('open-leave-modal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->dispatch('open-leave-picker');
    }

    #[On('leave-dates-updated')]
    public function updateLeaveDates($dates)
    {
        $this->leaveDates = $dates;
    }

    public function save()
    {
        $this->validate();

        $leavePlan = LeavePlan::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'note' => $this->note,
        ]);

        foreach ($this->leaveDates as $date) {
            LeavePlanDate::create([
                'leave_plan_id' => $leavePlan->id,
                'leave_date' => $date,
            ]);
        }

        $this->reset([
            'title',
            'note',
            'leaveDates',
        ]);

        $this->showModal = false;
        $this->dispatch('leave-plan-saved', message: 'Rencana cuti berhasil disimpan.');
    }

    public function cancel()
    {
        $this->showModal = false;
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
                            <i class="ri-calendar-todo-fill"></i> Rencana Cuti
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
                <div class="space-y-4 p-2">
                    <div>
                        <label class="text-sm font-medium">Judul</label>
                        <input type="text" wire:model="title" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                    <div wire:ignore>
                        <label class="text-sm font-medium">Tanggal Cuti</label>
                        <input id="leave_dates" type="text" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Pilih tanggal">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Keterangan</label>
                        <textarea rows="3" wire:model="note" class="mt-1 w-full rounded-lg border-gray-300"></textarea>
                    </div>
                    <div class="flex flex-col items-center justify-center text-center">
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="w-full bg-primary hover:bg-emerald-700 text-white rounded-xl w-10 h-10 disabled:opacity-50 disabled:cursor-not-allowed">
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
    $wire.on('open-leave-picker', () => {
        setTimeout(() => {
            const input = document.getElementById('leave_dates');
            if (!input || input._flatpickr) {
                return;
            }
            flatpickr(input, {
                mode: "multiple",
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange(selectedDates, dateStr, instance) {
                    let dates = selectedDates.map(date => {
                        return instance.formatDate(date, "Y-m-d");
                    });
                    $wire.dispatch('leave-dates-updated', {
                        dates: dates
                    });
                }
            });
        }, 100);
    });
</script>