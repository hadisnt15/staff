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
    <div wire:ignore id="default-carousel" class="relative w-full bg-neutral-primary-soft border border-default rounded-base shadow-xs md:mt-4 mt-2" data-carousel="slide">
        <!-- Carousel wrapper -->
        <div class="relative h-30 overflow-hidden rounded-base bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200">
            <!-- Item -->
            @forelse ($this->announcements as $announcement)
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <div class="px-2 py-1">
                        <div class="border-b border-gray-200">
                            <h5 class="font-bold text-sm md:text-base text-center text-emerald-900 p-1">{{ $announcement->announcement_title }}</h5>
                        </div>
                        <div>
                            <p class="text-sm md:text-base text-emerald-900 p-1 text-center">{{ $announcement->announcement_content }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <div class="px-2 py-1">
                        <div class="border-b border-gray-200">
                            <h5 class="font-bold text-sm md:text-base text-center text-emerald-900 p-1">Tidak Ada Informasi Terbaru</h5>
                        </div>
                        <div>
                            <p class="text-sm md:text-base text-emerald-900 p-1"></p>
                        </div>
                    </div>
                </div>
            @endforelse
            <!-- End Item -->
        </div>
        <!-- Slider indicators -->
        <div class="absolute z-30 flex -translate-x-1/2 bottom-3 left-1/2 space-x-2">
            @foreach ($this->announcements as $index => $announcement)
                <button 
                    type="button"
                    class="w-2.5 h-2.5 rounded-full transition-all duration-300 
                        {{ $index == 0 ? 'bg-emerald-600 scale-110' : 'bg-emerald-300' }}"
                    aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                    aria-label="Slide {{ $index + 1 }}"
                    data-carousel-slide-to="{{ $index }}">
                </button>
            @endforeach
        </div>
        <!-- Slider controls -->
        <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-base bg-emerald-500 group-hover:bg-gray-200 group-focus:ring-4 group-focus:ring-white opacity-60 group-focus:outline-none">
                <svg class="w-5 h-5 text-green-900 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                <span class="sr-only">Previous</span>
            </span>
        </button>
        <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-base bg-emerald-500 group-hover:bg-gray-200 group-focus:ring-4 group-focus:ring-white opacity-60 group-focus:outline-none">
                <svg class="w-5 h-5 text-green-900 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                <span class="sr-only">Next</span>
            </span>
        </button>
    </div>
</div>