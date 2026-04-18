<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('attendance_datetime');
            $table->enum('attendance_type', ['absen_masuk','absen_keluar','luar_kota','tidak_hadir']);
            $table->text('attendance_note')->nullable();
            $table->string('attendance_photo')->nullable();
            $table->enum('attendance_status', ['tertunda','disetujui','ditolak'])->default('disetujui');
            $table->integer('attendance_days_count')->default(0); // jumlah hari jika ada yg tugas luar kota, jika bukan tugas luar kota maka akan default 0
            $table->integer('attendance_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('attendance_lat', 10, 7)->nullable();
            $table->decimal('attendance_lng', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
