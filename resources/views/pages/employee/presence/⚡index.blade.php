<?php

use Livewire\Component;
use App\Services\AttendanceSummaryService;

new class extends Component
{
    public function yearOptions()
    {
        $years = AttendanceSummaryService::yearOptions();
        dd($years);
        return $years;
    }

    // public function mount()
    // {
    //     dd(AttendanceSummaryService::yearOptions());
    // }
};
?>

<div class="md:p-4 px-4 py-2 mx-auto max-w-7xl">
    <div class="relative overflow-x-auto shadow-md rounded-md border border-gray-200 py-2 px-2 bg-white">
        <nav class=" flex mb-4 px-5 py-3 border rounded-md bg-emerald-50 border-gray-200" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="ms-1 text-sm font-medium text-emerald-800 md:ms-2">
                            Data Presensi Karyawan
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <div>
            <livewire:employee.presence.dropdown/>
        </div>
        <div>
            <livewire:employee.presence.data/>
        </div>
    </div>
</div>