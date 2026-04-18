<?php

namespace App\Services;

class SalaryCalculationService
{
    public function calculate($components, $summary)
    {
        $result = [
            'salaries' => [],
            'total_salary' => 0,
            'penalties' => [],
            'total_penalty' => 0,
            'final' => 0,
        ];

        $presence = $summary['presenceCount'] ?? 0;
        $workday  = $summary['workdayCountNow'] ?? 0;
        $late     = $summary['lateCount'] ?? 0;
        $leave    = $summary['leaveCount'] ?? 0;

        foreach ($components as $row) {

            // =========================
            // 🔥 SUPPORT ARRAY & OBJECT
            // =========================
            if (is_array($row)) {
                $code  = $row['salary_code'] ?? null;
                $rule  = $row['salary_rule'] ?? null;
                $value = $row['value'] ?? 0;
                $name  = $row['salary_name'] ?? '-';
            } else {
                $code  = $row->salary->salary_code ?? null;
                $rule  = $row->salary->salary_rule ?? null;
                $value = $row->value ?? 0;
                $name  = $row->salary->salary_name ?? '-';
            }

            $amount = 0;

            // =========================
            // 💰 HITUNG GAJI
            // =========================
            if ($rule === 'tetap') {
                $amount = $value;
            } 
            elseif ($rule === 'perhari') {
                $amount = $value * $presence;
            } 
            elseif ($rule === 'bersyarat') {
                // $amount = ($presence == $workday) ? $value : 0;
                $amount = $value;
            }

            $result['salaries'][] = [
                'name' => $rule == 'perhari' ? $name . ' ('. $presence .' Hari Kerja)' : $name,
                'amount' => $amount,
                'code' => $code,
                'rule' => $rule,
                'base_value' => $value,
                'workday' => $workday,
            ];

            $result['total_salary'] += $amount;

            // =========================
            // ❌ PENALTY - TIDAK HADIR
            // =========================
            if ($leave > 0) {

                // Gaji Pokok
                if ($code === 'GP') {
                    $this->addPenalty(
                        $result,
                        'Potongan ' . $name . ' ('. $leave .' Hari Tidak Hadir)',
                        100000 * $leave * (-1)
                    );
                }

                // Premi Bulanan
                if ($code === 'PHB') {
                    $this->addPenalty(
                        $result,
                        'Potongan ' . $name . ' (Kehadiran Tidak Penuh)',
                        $value * -1
                    );
                }

                // Perhari
                if ($rule === 'perhari') {
                    $this->addPenalty(
                        $result,
                        'Potongan ' . $name . ' ('. $leave .' Hari Tidak Hadir)',
                        $value * $leave * -1
                    );
                }
            }

            // =========================
            // ❌ PENALTY - TELAT
            // =========================
            if ($late > 0 && $code === 'UMH') {
                $this->addPenalty(
                    $result,
                    'Potongan Uang Makan ('. $late .' Kali Tidak Tepat Waktu)',
                    $value * $late
                );
            }
        }

        $result['final'] = $result['total_salary'] + $result['total_penalty'];

        return $result;
    }

    private function addPenalty(&$result, $label, $amount)
    {
        $result['penalties'][] = [
            'name' => $label,
            'amount' => $amount,
        ];

        $result['total_penalty'] += $amount;
    }
}
