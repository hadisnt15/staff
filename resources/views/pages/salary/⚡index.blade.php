<?php

use Livewire\Component;
use App\Models\Holiday;
use App\Models\AttendanceSummary;
use Livewire\Attributes\Computed;
use App\Services\AttendanceSummaryService;
use App\Services\EmployeeSalaryService;
use App\Services\SalaryCalculationService;
use Livewire\Attributes\Title;

new #[Title('Gaji Saya')] class extends Component
{
    public $title = '';

    public $selectedPeriod = null;

    
    #[Computed]
    public function summary()
    {
        return AttendanceSummaryService::summary(
            auth()->id(),
            $this->selectedPeriod
        );
    }

    #[Computed]
    public function periodOptions()
    {
        return AttendanceSummaryService::periodOptions(auth()->id());
    }

    #[Computed]
    public function salaries()
    {
        [$year, $month, $start, $end] = AttendanceSummaryService::resolvePeriod($this->selectedPeriod);

        return EmployeeSalaryService::getSalaries(auth()->id(), $start, $end);
    }

    #[Computed]
    public function salaryResult()
    {
        return app(\App\Services\SalaryCalculationService::class)
            ->calculate($this->salaries, $this->summary);
    }

    public function render()
    {
        return $this->view([
        ]);
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
                            Slip Gaji {{ $selectedPeriod 
                                ? \Carbon\Carbon::createFromFormat('Y-m', $selectedPeriod)->locale('id')->translatedFormat('F Y') 
                                : now()->locale('id')->translatedFormat('F Y') }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <select wire:model.live="selectedPeriod" class="bg-gray-50 border border-gray-300 text-xs rounded-md text-gray-700 focus:ring focus:ring-indigo-200 py-2 px-2 w-30">
            {{-- <option value="">Bulan Sekarang</option> --}}
            @forelse ($this->periodOptions as $period)
                <option value="{{ $period['value'] }}">
                    {{ $period['label'] }}
                </option>
            @empty
                <option value="">Tidak ada data</option>
            @endforelse
        </select>


        <section class="bg-white rounded-md border border-gray-400 my-4">
            <div class="py-4 mx-auto">
                <h2 class="text-lg font-bold text-gray-900 md:text-xl uppercase text-center px-4">
                    Rincian Gaji Karyawan 
                </h2>
                <h2 class="mb-4 text-md font-bold text-gray-900 md:text-lg uppercase text-center border-b border-gray-400 px-4">
                    Periode
                    {{ $selectedPeriod 
                        ? \Carbon\Carbon::createFromFormat('Y-m', $selectedPeriod)->locale('id')->translatedFormat('F Y') 
                        : now()->locale('id')->translatedFormat('F Y') }}
                </h2>
                <div class="border-b border-gray-400">
                    <p class="text-sm font-bold text-gray-800 px-4">
                        Nama: {{ strtoupper(auth()->user()->name) }}
                    </p>
                    <p class="text-sm font-bold text-gray-800 px-4 mb-2">
                        Jabatan: {{ strtoupper(str_replace('_',' ',auth()->user()?->getRoleNames()->reject(fn ($r) => $r === 'super_admin')->join(', '))) }}
                    </p>
                </div>

                <div class="mx-auto grid md:grid-cols-2 gap-2 mt-4 block md:hidden">
                    <div class="border-b border-gray-500">
                        <span class="font-bold text-gray-500 text-sm px-4">Pendapatan</span>
                        @foreach ($this->salaryResult['salaries'] as $salary)
                            <div class="border-b border-gray-300 text-sm px-4">
                                <p class="font-semibold text-gray-500">{{ $salary['name'] }}</p>
                                <p class="font-semibold text-green-800 text-right">Rp {{ number_format($salary['amount'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                        <div class="flex justify-between font-bold text-sm px-4 mb-2">
                            <span class="text-gray-500">Total Pendapatan</span>
                            <span class="text-green-800 border-t border-gray-800">Rp {{ number_format($this->salaryResult['total_salary']) }}</span>
                        </div>
                    </div>
                    <div class="border-b border-gray-500">
                        <span class="font-bold text-gray-500 text-sm px-4">Potongan</span>
                        @foreach ($this->salaryResult['penalties'] as $salary)
                            <div class="border-b border-gray-300 text-sm px-4">
                                <p class="font-semibold text-gray-500">{{ $salary['name'] }}</p>
                                <p class="font-semibold text-yellow-600 text-right">Rp {{ number_format($salary['amount'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                        <div class="flex justify-between font-bold text-sm px-4 mb-2">
                            <span class="text-gray-500">Total Potongan</span>
                            <span class="text-yellow-600 border-t border-gray-800">Rp {{ number_format($this->salaryResult['total_penalty']) }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between text-sm px-4">
                        <span class="font-bold text-gray-800">Gaji Bersih</span>
                        <span class="font-bold text-green-800">Rp {{ number_format($this->salaryResult['final']) }}</span>
                    </div>
                    <div class="text-center text-sm">
                        <span class="font-semibold text-gray-800">Terbilang</span>
                        <span class="font-semibold text-green-800">{{ ucfirst(trim(terbilang($this->salaryResult['final']))) }} Rupiah</span>
                    </div>
                </div>

                {{-- desktop --}}
                <div class="md:block hidden">
                    <div class="mx-auto grid grid-cols-2 gap-2 my-2 px-4">
                        <div class="px-4">
                            <span class="font-bold text-gray-500 text-sm">Pendapatan</span>
                            @foreach ($this->salaryResult['salaries'] as $salary)
                                <div class="flex justify-between border-b border-gray-300 text-sm py-1">
                                    <span class="font-semibold text-gray-500">{{ $salary['name'] }}</span>
                                    <span class="font-semibold text-green-800">Rp {{ number_format($salary['amount'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between font-bold text-sm">
                                <span class="text-gray-500">Total Pendapatan</span>
                                <span class="text-green-800 border-t border-gray-800">Rp {{ number_format($this->salaryResult['total_salary']) }}</span>
                            </div>
                        </div>
                        <div class="px-4">
                            <span class="font-bold text-gray-500 text-sm">Potongan</span>
                            @foreach ($this->salaryResult['penalties'] as $salary)
                                <div class="flex justify-between border-b border-gray-300 text-sm py-1">
                                    <span class="font-semibold text-gray-500">{{ $salary['name'] }}</span>
                                    <span class="font-semibold text-yellow-600">Rp {{ number_format($salary['amount'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between font-bold text-sm">
                                <span class="text-gray-500">Total Potongan</span>
                                <span class="text-yellow-600 border-t border-gray-800">Rp {{ number_format($this->salaryResult['total_penalty']) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-400">
                        <div class="px-4 mt-2">
                            <div class="flex justify-between border-b border-gray-300 text-sm ">
                                <span class="font-bold text-gray-800">Gaji Bersih</span>
                                <span class="font-bold text-green-800">Rp {{ number_format($this->salaryResult['final']) }}</span>
                            </div>
                            <div class="text-center text-sm mt-2">
                                <span class="font-bold text-gray-800 border-b border-gray-300">Terbilang</span>
                                <span class="font-bold text-green-800 border-b border-gray-300">{{ ucfirst(trim(terbilang($this->salaryResult['final']))) }} Rupiah</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end desktop --}}
            </div>
        </section>
    </div>
</div>