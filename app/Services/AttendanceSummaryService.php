<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSummary;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AttendanceSummaryService
{
    public static function resolvePeriod(?string $selectedPeriod): array
    {
        if ($selectedPeriod) {
            [$year, $month] = explode('-', $selectedPeriod);
        } else {
            $year = now()->year;
            $month = now()->month;
        }

        $start = Carbon::create($year, $month)->startOfMonth();
        $end = Carbon::create($year, $month)->endOfMonth();

        return [$year, $month, $start, $end];
    }

    public static function summary(int $userId, ?string $selectedPeriod): array
    {
        $selectedPeriod = $selectedPeriod ?? now()->format('Y-m');

        [$year, $month, $start, $end] = self::resolvePeriod($selectedPeriod);

        $now = now();
        $endOrNow = $start->isSameMonth($now) ? $now : $end;

        $baseQuery = AttendanceSummary::query()->where('id_pengguna', $userId)->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        $presenceCount = (clone $baseQuery)->whereBetween('poin_kehadiran', [0, 5])->count();
        $lateCount = (clone $baseQuery)->whereBetween('poin_kehadiran', [1, 4])->count();
        $leaveCount = (clone $baseQuery)->whereBetween('poin_kehadiran', [6, 7])->count();
        $ontimeCount = $presenceCount - $lateCount;

        $holidayCountEnd = Holiday::whereBetween('holiday_date', [$start, $end])->count();
        $holidayCountNow = Holiday::whereBetween('holiday_date', [$start, $now])->count();

        $holidayCountEnd += Carbon::parse($start)->diffInDaysFiltered(
            fn (Carbon $date) => $date->isSunday(),
            Carbon::parse($end)
        );

        $holidayCountNow += Carbon::parse($start)->diffInDaysFiltered(
            fn (Carbon $date) => $date->isSunday(),
            Carbon::parse($now)
        );

        $dayCount = collect(CarbonPeriod::create($start, $end))->count();
        $dayCountNow = collect(CarbonPeriod::create($start, $endOrNow))->count();

        return [
            'dayCount' => $dayCount,
            'presenceCount' => $presenceCount,
            'lateCount' => $lateCount,
            'ontimeCount' => $ontimeCount,
            'leaveCount' => $start->isSameMonth($now) ? max(0, $leaveCount - $holidayCountNow) : max(0, $leaveCount - $holidayCountEnd),
            'workdayCount' => $dayCount - $holidayCountEnd,
            'workdayCountNow' => $start->isSameMonth($now) ? $dayCountNow - $holidayCountNow : $dayCount - $holidayCountEnd,
            'holidayCount' => $holidayCountEnd,
        ];
    }

    public static function periodOptions(int $userId)
    {
        $periods = DB::table('vdates as d')
            ->join('employee_salary_heads as h', function ($join) {
                $join->on('d.tanggal', '>=', 'h.start_date')
                    ->where(function ($q) {
                        $q->whereColumn('d.tanggal', '<=', 'h.end_date')
                        ->orWhereNull('h.end_date');
                    });
            })
            ->where('h.user_id', $userId)
            ->selectRaw('YEAR(d.tanggal) as year, MONTH(d.tanggal) as month')
            ->distinct()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        if ($periods->isEmpty()) {
            return collect();
        }

        return $periods->map(function ($item) {
            return [
                'value' => "{$item->year}-" . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                'label' => \Carbon\Carbon::create($item->year, $item->month)
                    ->locale('id')
                    ->translatedFormat('F Y'),
            ];
        });
    }

    public static function baseQuery(int $userId, ?string $selectedPeriod)
    {
        $selectedPeriod = $selectedPeriod ?? now()->format('Y-m');

        [$year, $month] = explode('-', $selectedPeriod);

        return AttendanceSummary::query()
            ->where('id_pengguna', $userId)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);
    }

    public static function yearOptions()
    {
        $years = AttendanceSummary::query()->selectRaw('YEAR(tanggal) as year')->distinct()->orderByDesc('year')->pluck('year');
        return $years;
    }

    public static function monthOptions(int $year)
    {
        $months = AttendanceSummary::query()->whereYear('tanggal', $year)->selectRaw('MONTH(tanggal) as month')->distinct()->orderByDesc('month')->pluck('month');
        return $months;
    }

    public static function allAttendanceSummary(int $year, ?int $month)
    {
        $firstAttendance = Attendance::query()
            ->selectRaw('user_id, DATE(MIN(attendance_datetime)) as first_date')
            ->groupBy('user_id');
        $attendanceSummary = AttendanceSummary::query()
            ->with('user')
            ->joinSub($firstAttendance, 'first_attendance', function ($join) {
                $join->on(
                    'vattendance_summaries.id_pengguna',
                    '=',
                    'first_attendance.user_id'
                );
            })
            ->whereYear('vattendance_summaries.tanggal', $year)
            ->whereMonth('vattendance_summaries.tanggal', $month)
            ->whereColumn(
                'vattendance_summaries.tanggal',
                '>=',
                'first_attendance.first_date'
            )
            ->select('vattendance_summaries.*')
            ->get()
            ->groupBy('id_pengguna');
        return $attendanceSummary;
    }
}
