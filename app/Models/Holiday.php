<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Holiday extends Model
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;
    
    protected $fillable = [
        'holiday_date',
        'holiday_note'
    ];
}
