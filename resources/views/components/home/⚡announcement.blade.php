<?php

use Livewire\Component;

use App\Models\Announcement;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Computed]
    public function announcements()
    {
        return Announcement::where('announcement_start_date', '<=', now())->where('announcement_end_date', '>=', now())->get();
    }
};
?>
<div x-data="{ show: false, title: '', content: '' }">
    <div id="default-carousel" wire:ignore class="relative w-full overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-white to-emerald-50 shadow-lg shadow-emerald-100 mt-4" data-carousel="slide">
        {{-- Carousel --}}
        <div class="relative h-32 overflow-hidden">
            @forelse ($this->announcements as $index => $announcement)
                <div data-carousel-item="{{ $index == 0 ? 'active' : '' }}" @click=" title = @js($announcement->announcement_title); content = @js($announcement->announcement_content); show = true; " class="{{ $index == 0 ? '' : 'hidden' }} cursor-pointer duration-500 ease-in-out px-5 py-4">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="inline-flex shrink-0 items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            <i class="ri-notification-3-fill"></i>
                        </div>
                        <h5 class="line-clamp-1 text-base font-semibold text-gray-800">{{ $announcement->announcement_title }}</h5>
                    </div>
                    <div class="my-2 h-px bg-emerald-100"></div>
                    <p class="line-clamp-2 text-sm leading-relaxed text-gray-600">
                        {{ Str::limit($announcement->announcement_content, 120) }}
                    </p>
                    
                </div>
            @empty
                <div data-carousel-item="active" class="px-5 py-4">
                    <div class="flex h-full flex-col items-center justify-center text-center">
                        <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                            <i class="ri-information-fill text-xl text-emerald-600"></i>
                        </div>
                        <h5 class="font-semibold text-gray-700">Tidak Ada Informasi Terbaru</h5>
                        <p class="mt-1 text-sm text-gray-500">Informasi terbaru akan muncul di sini.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Indicator --}}
        @if($this->announcements->count() > 1)
            <div class="absolute bottom-3 left-1/2 z-30 flex -translate-x-1/2 gap-2">
                @foreach ($this->announcements as $index => $announcement)
                    <button type="button" data-carousel-slide-to="{{ $index }}" aria-label="Slide {{ $index + 1 }}" class="h-2 w-2 rounded-full bg-emerald-300"></button>
                @endforeach
            </div>
        @endif
        
    </div>
    <div x-cloak x-show="show" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div @click.outside="show = false" class="w-full max-w-2xl rounded-xl bg-white shadow-xl mx-4">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h2 class="text-lg font-semibold" x-text="title"></h2>
                <button @click="show = false" class="text-xl text-gray-500 hover:text-black">✕</button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto p-6">
                <p class="whitespace-pre-line text-gray-700" x-text="content"></p>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        $nextTick(() => {
            const el = document.getElementById('default-carousel');

            console.log('init');

            let startX = 0;

            el.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            }, { passive: true });

            el.addEventListener('touchend', (e) => {
                const endX = e.changedTouches[0].clientX;
                const diff = startX - endX;

                if (Math.abs(diff) < 50) return;

                const carousel = FlowbiteInstances.getInstance('Carousel', 'default-carousel');

                console.log(carousel);

                if (!carousel) return;

                if (diff > 0) {
                    console.log('NEXT');
                    carousel.next();
                } else {
                    console.log('PREV');
                    carousel.prev();
                }
            }, { passive: true });
        });
    </script>
@endscript