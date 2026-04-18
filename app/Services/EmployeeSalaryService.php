<?php

namespace App\Services;

use App\Models\EmployeeSalaryHead;

class EmployeeSalaryService
{
    public static function getSalaries(int $userId, $start, $end)
    {
        $head = EmployeeSalaryHead::query()
            ->where('user_id', $userId)
            ->where('start_date', '<=', $end)
            ->where(function ($q) use ($start) {
                $q->whereNull('end_date')
                ->orWhere('end_date', '>=', $start);
            })
            ->with(['rows.salary'])
            ->first();

        if (!$head) return [];

        return $head->rows->map(function ($row) {
            return [
                'salary_code' => $row->salary->salary_code,
                'salary_rule' => $row->salary->salary_rule,
                'salary_name' => $row->salary->salary_name,
                'value' => $row->value,
            ];
        })->toArray();
    }
}
