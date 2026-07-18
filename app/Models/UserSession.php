<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable=[
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity',
        'is_active',
        'disconnected_at'
    ];


    protected $casts=[
        'last_activity'=>'datetime',
        'disconnected_at'=>'datetime',
        'is_active'=>'boolean'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
