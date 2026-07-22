<?php

use Livewire\Component;

new class extends Component
{
    
};
?>

<div>
    <div x-data="{ showRule: true }" x-effect="
            if (showRule) {
                setTimeout(() => {
                    const carousel = FlowbiteInstances.getInstance('Carousel', 'guide-carousel');
                    if (carousel) carousel.slideTo(0);
                }, 100);
            }
        ">
        <div class="flex justify-end mb-1">
            <button @click="showRule = true" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition">
                <i class="ri-information-2-fill"></i> Panduan Penggunaan
            </button>
        </div>
        <div x-show="showRule" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display: none;">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between border-b px-6 py-2">
                    <h2 class="text-md font-semibold">Panduan Penggunaan</h2>
                    <button @click="showRule = false" class="text-red-500 hover:text-red-900 text-md font-semibold">✕</button>
                </div>
                <div class="overflow-y-auto">
                    <div class="">
                        <div id="guide-carousel" class="py-1 relative w-full overflow-hidden rounded-xl" data-carousel="static">
                            <!-- Carousel -->
                            <div class="relative overflow-hidden h-[380px] md:h-[520px] rounded-xl">
                                @foreach(range(1,6) as $i)
                                    <div data-carousel-item="{{ $i == 1 ? 'active' : '' }}" class="{{ $i == 1 ? '' : 'hidden' }} duration-500 ease-in-out">
                                        <img src="{{ asset("guide/Slide-$i.png") }}" alt="Slide {{ $i }}" class="mx-auto max-h-[70vh] w-auto rounded-xl" draggable="false">
                                        @if ($i == 2)
                                            <a href="{{ route('face-registration') }}" class="absolute right-[5%] bottom-[7%] rounded-lg bg-emerald-600 px-2 py-1 text-xs font-semibold text-white shadow hover:bg-emerald-700">
                                                Registrasikan Wajah
                                            </a>
                                        @endif

                                        @if ($i == 6)
                                            <a href="{{ route('leave-plan') }}" class="absolute right-[5%] bottom-[7%] rounded-lg bg-emerald-600 px-2 py-1 text-xs font-semibold text-white shadow hover:bg-emerald-700">
                                                Ajukan Rencana Cuti
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <!-- Indicator -->
                            <div class="mt-1 flex justify-center gap-2">
                                @foreach(range(0,5) as $i)
                                    <button type="button" data-carousel-slide-to="{{ $i }}" class="h-2.5 w-2.5 rounded-full bg-gray-300"></button>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-1 text-center text-xs text-gray-500">
                            Geser ke kiri atau kanan untuk melihat panduan berikutnya.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $nextTick(() => {
            const el = document.getElementById('guide-carousel');
            console.log('init');
            let startX = 0;
            el.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            }, { passive: true });
            el.addEventListener('touchend', (e) => {
                const endX = e.changedTouches[0].clientX;
                const diff = startX - endX;
                if (Math.abs(diff) < 50) return;
                const carousel = FlowbiteInstances.getInstance('Carousel', 'guide-carousel');
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