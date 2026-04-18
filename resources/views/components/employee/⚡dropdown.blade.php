<?php

use Livewire\Component;

use App\Models\User;
use App\Services\AttendanceSummaryService;
use Livewire\Attributes\Computed;

new class extends Component
{
    public $selectedUser;
    public $selectedPeriod;

    #[Computed]
    public function users()
    {
        return User::with('roles')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super_admin');
            })
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->roles->pluck('name')->join(', ')
                ];
            });
    }

    #[Computed]
    public function userPeriods()
    {
        if (empty($this->selectedUser)) return [];

        return AttendanceSummaryService::periodOptions((int) $this->selectedUser);
    }

    public function updatedSelectedUser($value)
    {
        $this->selectedPeriod = null;

        $this->dispatch('filterChanged', [
            'user' => $this->selectedUser,
            'period' => $this->selectedPeriod,
        ]);
    }

    public function updatedSelectedPeriod($value)
    {
        $this->dispatch('filterChanged', [
            'user' => $this->selectedUser,
            'period' => $this->selectedPeriod,
        ]);
    }
};
?>

<div>
    <select wire:model.live="selectedUser" class="bg-gray-50 border border-gray-300 text-xs rounded-md text-gray-700 focus:ring focus:ring-indigo-200 py-2 px-2 w-45">
        <option value="">Pilih Karyawan</option>
        @forelse ($this->users as $user)
            <option value="{{ $user['id'] }}">
                {{ $user['name'] }}
            </option>
        @empty
            <option value="">Tidak ada data karyawan</option>
        @endforelse
    </select>

    <select wire:model.live="selectedPeriod" class="bg-gray-50 border border-gray-300 text-xs rounded-md text-gray-700 focus:ring focus:ring-indigo-200 py-2 px-2 w-45">
        <option value="">Pilih Periode</option>
        @forelse ($this->userPeriods as $userPeriod)
            <option value="{{ $userPeriod['value'] }}">
                {{ $userPeriod['label'] }}
            </option>
        @empty
            <option value="">Tidak ada data periode</option>
        @endforelse
    </select>
</div>