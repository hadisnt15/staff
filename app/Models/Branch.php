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

    protected $casts = [
        'work_start_time' => 'datetime:H:i:s',
        'work_end_time' => 'datetime:H:i:s',
        'work_end_time_weekend' => 'datetime:H:i:s',
        'break_start_time' => 'datetime:H:i:s',
        'break_end_time' => 'datetime:H:i:s',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
