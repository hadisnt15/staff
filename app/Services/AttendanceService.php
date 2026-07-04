<?php

namespace App\Services;

use App\Models\Attendance;

class AttendanceService
{
    public static function getTodayLog(int $userId)
    {
        return Attendance::where('user_id', $userId)
            ->whereDate('attendance_datetime', today())
            ->latest('attendance_datetime')
            ->first();
    }

    public static function getType(?Attendance $log): ?string
    {
        if (!$log) return null;

        return match ($log->attendance_type) {
            'absen_masuk' => $log->attendance_break ? 'Presensi Selesai Istirahat' : ($log->attendance_permission ? 'Presensi Selesai Izin' : 'Presensi Datang'),
            'absen_keluar' => $log->attendance_break ? 'Presensi Mulai Istirahat' : ($log->attendance_permission ? 'Presensi Mulai Izin' : 'Presensi Pulang'),
            'luar_kota' => 'Presensi Luar Kota',
            'tidak_hadir' => 'Presensi Tidak Hadir',
            default => null
        };
    }

    public static function getButtonState(?Attendance $log, $user): array
    {
        $isCheckoutFinal = $log && $log->attendance_type === 'absen_keluar' && !$log->attendance_break && !$log->attendance_permission;
        $isAbsensce = $log && $log->attendance_type === 'tidak_hadir';
        return [
            'checkin' => $isAbsensce || $isCheckoutFinal || $log?->attendance_type === 'absen_masuk',
            'checkout' => $isAbsensce || $isCheckoutFinal || !$log || $log?->attendance_type === 'absen_keluar',
            'businessTrip' => $isAbsensce || $isCheckoutFinal || !$user->hasAnyRole(['driver', 'salesman']),
            'leave' => $isAbsensce || $isCheckoutFinal || (bool)$log,
        ];
    }

    public static function getGreeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 12 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Pagi',
        };
    }
    
    public function getMessage(array $summary): array
    {
        $presence = $summary['presenceCount'] ?? 0;
        $late = $summary['lateCount'] ?? 0;
        $leave = $summary['leaveCount'] ?? 0;
        $workday = $summary['workdayCount'] ?? 0;

        if ($presence === 0) {
            return [
                'type' => 'info',
                'message' => '📊 Belum ada catatan kehadiran bulan ini.',
            ];
        }

        $presenceRatio = $workday > 0 ? $presence / $workday : 0;
        $lateRatio = $presence > 0 ? $late / $presence : 0;
        $leaveRatio = $workday > 0 ? $leave / $workday : 0;

        // Kehadiran sangat rendah
        if ($leaveRatio >= 0.30) {
            return [
                'type' => 'danger',
                'message' => '🚨 Kehadiran Anda masih rendah bulan ini. Mari tingkatkan kedisiplinan agar kehadiran Anda semakin baik.',
            ];
        }

        // Terlalu sering terlambat
        if ($lateRatio >= 0.40) {
            return [
                'type' => 'warning',
                'message' => '⏰ Anda cukup sering terlambat bulan ini. Cobalah mengatur waktu keberangkatan lebih baik.',
            ];
        }

        // Hadir sempurna & tidak pernah terlambat
        if ($presence == $workday && $late == 0) {
            return [
                'type' => 'success',
                'message' => '🏆 Luar biasa! Kehadiran Anda sempurna tanpa keterlambatan bulan ini.',
            ];
        }

        // Hadir minimal 95% dan terlambat maksimal 1 kali
        if ($presenceRatio >= 0.95 && $late <= 1) {
            return [
                'type' => 'success',
                'message' => '🌟 Kehadiran Anda sangat baik. Pertahankan konsistensi ini!',
            ];
        }

        // Hadir minimal 90%
        if ($presenceRatio >= 0.90) {
            return [
                'type' => 'success',
                'message' => '👍 Kehadiran Anda sudah baik. Terus pertahankan kedisiplinan agar semakin konsisten.',
            ];
        }

        // Ada keterlambatan tetapi tidak berlebihan
        if ($late > 0) {
            return [
                'type' => 'warning',
                'message' => '⚠️ Anda tercatat terlambat beberapa kali. Semoga bulan berikutnya bisa lebih tepat waktu.',
            ];
        }

        return [
            'type' => 'info',
            'message' => '🙂 Kehadiran Anda cukup baik. Tetap jaga konsistensi dan semangat bekerja.',
        ];
    }


    public static function getAttendances(int $userId)
    {
        return Attendance::where('user_id', $userId)
            ->whereMonth('attendance_datetime', now()->month)
            ->whereYear('attendance_datetime', now()->year)
            ->latest()
            ->paginate(10);
    }
}
