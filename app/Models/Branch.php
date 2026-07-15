<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_active',
        'timezone',
        'work_start_time',
        'work_end_time',
        'work_end_time_weekend',
        'break_start_time',
        'break_end_time',
        'lat',
        'lng'
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
