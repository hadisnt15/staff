<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeavePlan extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'note',
        'status',
        'approved_by',
        'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dates()
    {
        return $this->hasMany(LeavePlanDate::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
