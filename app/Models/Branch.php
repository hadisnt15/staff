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
        'timezone'
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
