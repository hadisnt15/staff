<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'salary_name',
        'salary_type',
        'salary_note'
    ];

    public function employeeSalaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }
}
