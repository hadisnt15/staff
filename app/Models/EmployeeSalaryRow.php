<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryRow extends Model
{
    protected $fillable = [
        'employee_salary_head_id',
        'salary_id',
        'value'
    ];

    public function head()
    {
        return $this->belongsTo(EmployeeSalaryHead::class, 'employee_salary_head_id');
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salary_id');
    }
}
