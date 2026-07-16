<?php

use Livewire\Component;
use App\Models\LeavePlan;

new class extends Component
{
    public string $successMessage = '';

    #[\Livewire\Attributes\On('leave-plan-saved')]
    public function showSuccess($message)
    {
        $this->successMessage = $message;
    }

    public function getLeavePlansProperty()
    {
        return LeavePlan::with('dates')->where('user_id', auth()->id())->latest()->get();
    }

    public function openModal()
    {
        $this->dispatch('open-leave-modal');
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
                            Rencana Cuti
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <button wire:click="openModal" class="text-xs rounded-md px-3 py-2 bg-emerald-800 hover:bg-emerald-500 font-medium text-white">
            <i wire:loading wire:target="openModal" class="ri-loader-4-line animate-spin"></i>
            Ajukan Rencana Cuti
        </button>
    
        @if($successMessage)
        <div class="rounded-md border border-green-300 bg-green-100 p-3 my-2">
            <div class="font-semibold text-green-800">
                ✅ Berhasil
            </div>
            <div class="text-sm text-green-700">
                {{ $successMessage }}
            </div>
        </div>
        @endif
        <livewire:leave-plan.modal />
        <div class="mt-2">
            {{-- DESKTOP TABLE --}}
            <div class="hidden md:block overflow-x-auto border rounded-md">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs font-bold text-white uppercase bg-emerald-800">
                        <tr>
                            <th class="px-2 py-2 w-2/12">Judul</th>
                            <th class="px-2 py-2 w-4/12">Tanggal</th>
                            <th class="px-2 py-2 w-5/12">Keterangan</th>
                            <th class="px-2 py-2 w-1/12">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y bg-white">
                        @forelse($this->leavePlans as $plan)
                            <tr>
                                <td class="px-2 py-2 text-sm font-medium">
                                    {{ $plan->title }}
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($plan->dates as $date)
                                            <span class="text-emerald-700">
                                                {{ \Carbon\Carbon::parse($date->leave_date)->format('d M Y') }},
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    {{ $plan->note ?? '-' }}
                                </td>
                                <td class="px-2 py-2 text-sm text-gray-500">
                                    {{ $plan->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada rencana cuti.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- MOBILE CARD --}}
            <div class="space-y-3 md:hidden">

                @forelse($this->leavePlans as $plan)

                    <div class="rounded-xl border bg-white p-4 shadow-sm">

                        <div class="font-semibold">
                            {{ $plan->title }}
                        </div>


                        <div class="mt-2 flex flex-wrap gap-1">

                            @foreach($plan->dates as $date)

                                <span class="rounded bg-emerald-100 px-2 py-1 text-xs text-emerald-700">
                                    {{ \Carbon\Carbon::parse($date->leave_date)->format('d M Y') }}
                                </span>

                            @endforeach

                        </div>


                        @if($plan->note)

                            <div class="mt-3 text-sm text-gray-600">
                                {{ $plan->note }}
                            </div>

                        @endif


                        <div class="mt-3 text-xs text-gray-400">
                            {{ $plan->created_at->format('d M Y') }}
                        </div>

                    </div>


                @empty

                    <div class="rounded-xl border p-5 text-center text-gray-500">
                        Belum ada rencana cuti.
                    </div>

                @endforelse


            </div>

        </div>
    </div>
</div>
