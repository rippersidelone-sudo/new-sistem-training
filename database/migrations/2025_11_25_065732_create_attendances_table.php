<?php
// 2024_01_01_000008_create_attendances_table.php

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
            $table->date('attendance_date');
            $table->dateTime('checkin_time')->nullable();
            $table->dateTime('checkout_time')->nullable();
            $table->enum('status', ['Checked-in', 'Approved', 'Absent'])->default('Checked-in');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['batch_id', 'user_id', 'attendance_date'], 'attendance_unique');
            
            $table->index('batch_id');
            $table->index('user_id');
            $table->index('attendance_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

