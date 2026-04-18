<?php
declare(strict_types=1);

namespace App\Data;

use App\Models\Attendance;
use DateTimeInterface;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AttendanceData extends Data
{
    public function __construct(
        public int $id,
        public DateTimeInterface $attendance_datetime,
        public string $attendance_type,
        public string|Optional|null $attendance_note,
        public string|Optional|null $attendance_photo,
        public string $attendance_status,
        public float $attendance_lat,
        public float $attendance_lng,
    ) {}

    public static function fromModel(Attendance $attendance) : self
    {
        return new self(
            $attendance->id,
            $attendance->attendance_datetime,
            $attendance->attendance_type,
            $attendance->attendance_note,
            $attendance->attendance_photo,
            $attendance->attendance_status,
            floatval($attendance->attendance_lat),
            floatval($attendance->attendance_lng),
        );
    }
}
