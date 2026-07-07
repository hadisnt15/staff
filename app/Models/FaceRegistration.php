<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FaceRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'front_path',
        'left_path',
        'right_path',
        'up_path',
        'down_path',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_at' => null,
        ]);
    }

    public function isLocked(): bool
    {
        return $this->status === 'approved';
    }

    public function canEdit(): bool
    {
        return $this->status !== 'approved';
    }
}
