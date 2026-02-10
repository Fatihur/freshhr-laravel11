<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Status untuk check-in (detailed)
            $table->enum('status_in', [
                'valid_on_time',       // Dalam radius, tepat waktu
                'valid_late',          // Dalam radius, terlambat
                'invalid_out_of_radius', // Di luar radius
                'invalid_no_schedule',   // Tidak ada jadwal
                'invalid_on_leave',      // Sedang cuti
                'invalid_already_in',    // Sudah check-in
                'invalid_early',         // Terlalu awal (opsional)
            ])->nullable()->after('time_in');

            // Status untuk check-out (detailed)
            $table->enum('status_out', [
                'valid_normal',        // Normal checkout
                'valid_early',         // Pulang awal
                'invalid_not_yet_in',  // Belum check-in
                'invalid_already_out', // Sudah check-out
            ])->nullable()->after('time_out');

            // Koordinat untuk check-out terpisah
            $table->decimal('check_out_latitude', 10, 8)->nullable()->after('longitude');
            $table->decimal('check_out_longitude', 11, 8)->nullable()->after('check_out_latitude');
            $table->string('check_out_photo')->nullable()->after('photo');

            // Alasan jika invalid
            $table->text('rejection_reason')->nullable()->after('status_out');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'status_in',
                'status_out',
                'check_out_latitude',
                'check_out_longitude',
                'check_out_photo',
                'rejection_reason',
            ]);
        });
    }
};
