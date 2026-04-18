<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'user_id',
        'salary_id',
        'value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salary_id');
    }
}
