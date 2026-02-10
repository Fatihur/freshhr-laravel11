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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'hr_admin', 'dept_head', 'employee'])->default('employee')->after('email');
            $table->foreignId('employee_id')->nullable()->after('role')->constrained()->onDelete('set null');
            $table->string('avatar')->nullable()->after('employee_id');
            $table->timestamp('last_login')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn(['role', 'employee_id', 'avatar', 'last_login']);
        });
    }
};
