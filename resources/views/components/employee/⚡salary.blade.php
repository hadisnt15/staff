<?php

use Livewire\Component;

use App\Services\SalaryCalculationService;
use App\Services\AttendanceSummaryService;
use App\Services\EmployeeSalaryService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;

new class extends Component
{
    #[Reactive]
    public $selectedUser = null;

    #[Reactive]
    public $selectedPeriod = null;

    #[Computed]
    public function userSummaries()
    {
        if (empty($this->selectedUser) || empty($this->selectedPeriod)) {
            return [
                'dayCount' => 0,
                'presenceCount' => 0,
                'lateCount' => 0,
                'ontimeCount' => 0,
                'leaveCount' => 0,
                'workdayCount' => 0,
                'workdayCountNow' => 0,
                'holidayCount' => 0,
            ];
        }
        return AttendanceSummaryService::summary(
            (int) $this->selectedUser,
            $this->selectedPeriod
        );
    }
    
    #[Computed]
    public function salaries()
    {
        [$year, $month, $start, $end] = AttendanceSummaryService::resolvePeriod($this->selectedPeriod);

        return EmployeeSalaryService::getSalaries((int) $this->selectedUser, $start, $end);
    }

    #[Computed]
    public function salaryResult()
    {
        return app(\App\Services\SalaryCalculationService::class)
            ->calculate($this->salaries, $this->userSummaries);
    }
};
?>

<div>
    <h2 class="text-sm font-bold text-gray-800 text-center px-4">
        Rincian Penggajian
    </h2>

    {{-- mobile --}}
    <div class="mx-auto block md:hidden">
        <div class="border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl py-2 mb-2">
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
        <div class="border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl py-2 mb-2">
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
        <div class="border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl py-2">
            <div class="flex justify-between text-sm px-4">
                <span class="font-bold text-gray-800">Gaji Bersih</span>
                <span class="font-bold text-green-800">Rp {{ number_format($this->salaryResult['final']) }}</span>
            </div>
            <div class="text-center text-sm">
                <span class="font-semibold text-gray-800">Terbilang</span>
                <span class="font-semibold text-green-800">{{ ucfirst(trim(terbilang($this->salaryResult['final']))) }} Rupiah</span>
            </div>
        </div>
    </div>
    {{-- end mobile --}}

    {{-- desktop --}}
    <div class="mx-auto grid-cols-2 gap-2 md:grid hidden">
        <div class="border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl px-4">
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
        <div class="border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl px-4">
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
    <div class="hidden md:block border border-emerald-600 bg-gradient-to-br from-white via-emerald-50 to-emerald-100 rounded-xl px-4 py-2 mt-2">
        <div class="flex justify-between border-b border-gray-300 text-sm ">
            <span class="font-bold text-gray-800">Gaji Bersih</span>
            <span class="font-bold text-green-800">Rp {{ number_format($this->salaryResult['final']) }}</span>
        </div>
        <div class="text-center text-sm mt-2">
            <span class="font-bold text-gray-800 border-b border-gray-300">Terbilang</span>
            <span class="font-bold text-green-800 border-b border-gray-300">{{ ucfirst(trim(terbilang($this->salaryResult['final']))) }} Rupiah</span>
        </div>
    </div>
    {{-- end desktop --}}
</div>