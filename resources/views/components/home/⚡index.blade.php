<?php

use Livewire\Component;

new class extends Component
{
    public $title = 'Kehadiran';
};
?>

<div>
    <input wire:model="title" type="text">
    <button wire:click="save">Save Post</button>
</div>