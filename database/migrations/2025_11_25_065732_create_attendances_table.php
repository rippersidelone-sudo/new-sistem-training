<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();

            $table->date('attendance_date');
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();

            $table->enum('status', ['Checked-in', 'Approved', 'Rejected', 'Absent'])->default('Checked-in');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'user_id', 'attendance_date']);
            $table->index('status');
            $table->index(['batch_id', 'status'], 'idx_attendance_batch_status');
            $table->index('validated_by', 'idx_attendance_validated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
