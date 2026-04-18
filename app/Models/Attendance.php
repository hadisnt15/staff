<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_datetime',
        'attendance_break',
        'attendance_permission',
        'attendance_type',
        'attendance_note',
        'attendance_photo',
        'attendance_status',
        'attendance_days_count',
        'attendance_approved_by',
        'attendance_lat',
        'attendance_lng'
    ];

    protected $casts = [
        'attendance_datetime' => 'datetime',
    ];

    // public function holiday()
    // {
    //     return $this->hasOne(Holiday::class, 'holiday_date')
    // }
}
