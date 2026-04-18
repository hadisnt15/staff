<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryHead extends Model
{
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rows()
    {
        return $this->hasMany(EmployeeSalaryRow::class);
    }
}
