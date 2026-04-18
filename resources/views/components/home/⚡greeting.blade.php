<?php

use Livewire\Component;

use App\Services\AttendanceService;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Computed]
    public function greeting()
    {
        return AttendanceService::getGreeting();
    }
};
?>

<div>
    <span class="text-sm md:text-base font-semibold tracking-tight text-fg-success-strong">{{ $this->greeting }}</span>
</div>