<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\AttendanceSummaryService;

new class extends Component
{
    public $selectedYear = null;
    public $selectedMonth = null;

    #[Computed]
    public function yearOptions()
    {
        $years = AttendanceSummaryService::yearOptions();
        return $years;
    }
    
    #[Computed]
    public function monthOptions()
    {
        if (!$this->selectedYear) {
            return collect();
        }

        $months = AttendanceSummaryService::monthOptions($this->selectedYear);
        return $months;
    }

    public function updatedSelectedYear()
    {
        if ($this->selectedYear && $this->selectedMonth) {
            $this->dispatch(
                'attendance-filter-changed',
                year: $this->selectedYear,
                month: $this->selectedMonth
            );
        }
    }

    public function updatedSelectedMonth()
    {
        if ($this->selectedYear && $this->selectedMonth) {
            $this->dispatch(
                'attendance-filter-changed',
                year: $this->selectedYear,
                month: $this->selectedMonth
            );
        }
    }
};
?>

<div>
    <select wire:model.live="selectedYear" class="bg-gray-50 border border-gray-300 text-xs rounded-md text-gray-700 focus:ring focus:ring-indigo-200 py-2 px-2 w-45">
        <option value="">Pilih Tahun</option>
        @forelse ($this->yearOptions as $yearOption)
            <option value="{{ $yearOption }}">
                {{ $yearOption }}
            </option>
        @empty
            <option value="">Tidak ada data periode</option>
        @endforelse
    </select>
    <select wire:model.live="selectedMonth" class="bg-gray-50 border border-gray-300 text-xs rounded-md text-gray-700 focus:ring focus:ring-indigo-200 py-2 px-2 w-45">
        <option value="">Pilih Bulan</option>
        @forelse ($this->monthOptions as $monthOption)
            <option value="{{ $monthOption }}">
                {{ $monthOption }}
            </option>
        @empty
            <option value="">Tidak ada data periode</option>
        @endforelse
    </select>
</div>