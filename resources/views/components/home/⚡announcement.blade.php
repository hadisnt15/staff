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

<div>
    <div wire:ignore id="default-carousel" class="relative w-full overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-white to-emerald-50 shadow-lg shadow-emerald-100 md:mt-4 mt-2"
    data-carousel="slide">
        {{-- Carousel --}}
        <div class="relative h-32 overflow-hidden">
            @forelse ($this->announcements as $announcement)
                <div class="hidden duration-500 ease-in-out px-5 py-4" data-carousel-item>
                    {{-- Badge --}}
                    <div class="mb-2 inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <i class="ri-notification-3-fill mr-1"></i> Informasi
                    </div>
                    {{-- Title --}}
                    <h5 class="text-base text-center font-bold text-gray-800 line-clamp-1">
                        {{ $announcement->announcement_title }}
                    </h5>
                    {{-- Divider --}}
                    <div class="my-2 h-px bg-emerald-100"></div>
                    {{-- Content --}}
                    <p class="text-sm leading-relaxed text-gray-600 line-clamp-2">
                        {{ $announcement->announcement_content }}
                    </p>
                </div>
            @empty
                <div class="hidden duration-500 ease-in-out px-5 py-4" data-carousel-item>
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
        <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-2">
            @foreach ($this->announcements as $index => $announcement)
                <button type="button" data-carousel-slide-to="{{ $index }}" class="h-2 rounded-full transition-all duration-300 {{ $index == 0 ? 'w-6 bg-emerald-600' : 'w-2 bg-emerald-200 hover:bg-emerald-400' }}"></button>
            @endforeach
        </div>

        {{-- Previous --}}
        <button type="button" class="absolute left-2 top-1/2 -translate-y-1/2" data-carousel-prev>
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow border border-emerald-100 transition hover:bg-emerald-50">
                <i class="ri-arrow-left-s-line text-xl text-emerald-700"></i>
            </div>
        </button>

        {{-- Next --}}
        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2" data-carousel-next>
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow border border-emerald-100 transition hover:bg-emerald-50">
                <i class="ri-arrow-right-s-line text-xl text-emerald-700"></i>
            </div>
        </button>
    </div>
</div>