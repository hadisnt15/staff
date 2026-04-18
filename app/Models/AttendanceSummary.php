<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    protected $table = 'vattendance_summaries';

    public $timestamps = false;

    protected $guarded = [];
}
