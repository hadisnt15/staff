<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeavePlanDate extends Model
{
    protected $fillable = [
        'leave_plan_id',
        'leave_date',
    ];

    protected $casts = [
        'leave_date' => 'date',
    ];

    public function leavePlan()
    {
        return $this->belongsTo(LeavePlan::class);
    }
}
