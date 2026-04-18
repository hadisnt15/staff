<?php

use Livewire\Component;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;

new #[Title('Rekap Data Karyawan')] class extends Component
{
    public $selectedUser = null;
    public $selectedPeriod = null;

    #[On('filterChanged')]
    public function updateFilter($data)
    {
        $this->selectedUser = $data['user'];
        $this->selectedPeriod = $data['period'];
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
                            Data Presensi dan Penggajian Karyawan
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <div>
            <livewire:employee.dropdown/>
        </div>

        @if ($selectedUser && $selectedPeriod)
            <div class="my-2">
                <div class="font-semibold text-gray-800">
                    <h5 class="py-2 text-center">
                        Data Presensi dan Penggajian Karyawan Periode {{ $this->selectedPeriod ? (\Carbon\Carbon::createFromFormat('Y-m', $this->selectedPeriod)->locale('id')->translatedFormat('F Y')) : '' }} 
                    </h5>
                    <div class="py-2 md:px-2">
                        <livewire:employee.selected :selectedUser="$selectedUser"/>
                    </div>
                </div>
                <div class="p-1 mt-4">
                    <livewire:employee.presence :selectedUser="$selectedUser" :selectedPeriod="$selectedPeriod"/>
                </div>
                <div class="p-1 mt-4">
                    <livewire:employee.salary :selectedUser="$selectedUser" :selectedPeriod="$selectedPeriod"/>
                </div>
                {{-- <div class="p-1 mt-4">
                    <livewire:employee.detail :selectedUser="$selectedUser" :selectedPeriod="$selectedPeriod"/>
                </div> --}}
            </div>
        @else
            <div class="text-center text-sm text-gray-500 py-6">
                Silakan pilih karyawan dan periode terlebih dahulu
            </div>
        @endif
    </div>
</div>
