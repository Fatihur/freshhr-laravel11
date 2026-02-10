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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['annual', 'sick', 'emergency', 'maternity', 'other'])->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->string('handover_to')->nullable();
            $table->text('handover_notes')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->boolean('approval_dept_head')->default(false);
            $table->boolean('approval_hrm')->default(false);
            $table->boolean('approval_gm')->default(false);
            $table->timestamp('dept_head_approved_at')->nullable();
            $table->timestamp('hrm_approved_at')->nullable();
            $table->timestamp('gm_approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
