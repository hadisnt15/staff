<?php
declare(strict_types=1);

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Halaman Utama')] class extends Component
{
    public $title = '';

    public function getTitle()
    {
        return 'Slip Gaji Saya';
    }
};
?>

<div>
    <div class="md:p-4 px-4 py-2 mx-auto max-w-7xl">
        <div class="bg-neutral-primary-soft block w-full border border-emerald-50 rounded-base shadow-lg">
            <div class="px-2 py-1 border-b border-gray-200 bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200 rounded-t-xl">
                <div class="flex justify-between">
                    <livewire:home.greeting />
                    <span wire:ignore class="bg-success-soft text-fg-success-strong text-xs md:text-base font-semibold px-1.5 py-0.5 rounded border border-emerald-200" id="clockIndex"></span>
                </div>    
                <div>
                    <span class="text-fg-success-strong font-bold text-sm md:text-base"> @auth {{ auth()->user()->name }} @else Anda Belum Login @endauth</span>
                </div>
                <livewire:home.rule />
            </div>

            {{-- face reg status --}}
            <livewire:home.face-reg-status />

            {{-- today log --}}
            <livewire:home.log />

            <div class="px-2 py-1">
                <span wire:ignore class="font-semibold md:font-bold text-md tracking-tight text-fg-success-strong">Rekap Presensi {{ now()->translatedFormat('F Y') }}</span>
            </div>
            <div class="max-w-7xl mx-auto rounded-2xl py-1 md:px-8 px-4">
                {{-- rekap absensi (diagram) --}}
                <livewire:home.summary />

                {{-- message --}}
                <livewire:home.message />
            </div>
        </div>

        {{-- button --}}
        <livewire:home.button />

        {{-- announcement --}}
        <livewire:home.announcement />
        
        {{-- latest --}}
        <livewire:home.latest />
    </div>
    {{-- modal --}}
    <livewire:home.modal />
</div>
<script>
    function updateClock() {
        // const now = new Date();
        const now = new Date(new Date().toLocaleString("en-US", {
            timeZone: branchTimezone
        }));

        const date = now.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });

        const hour = String(now.getHours()).padStart(2, '0');
        const minute = String(now.getMinutes()).padStart(2, '0');
        const second = String(now.getSeconds()).padStart(2, '0');

        const formatted = `${date} • ${hour}:${minute}:${second}`;

        const el = document.getElementById('clockIndex');
        if (el) el.innerText = formatted;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>