<?php

use Livewire\Component;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;

new class extends Component
{
    #[Reactive]
    public $selectedUser = null;

    #[Computed]
    public function selectedUserData()
    {
        if (empty($this->selectedUser)) return null;

        return User::with('roles')->find($this->selectedUser);
    }
};
?>

<div>
    <p class="text-sm">Nama: {{ $this->selectedUserData ? strtoupper($this->selectedUserData->name) : '' }}</p>
    <p class="text-sm">Jabatan: {{ $this->selectedUserData ? strtoupper($this->selectedUserData->roles->pluck('name')->join(', ')) : '' }}</p>
</div>