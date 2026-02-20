<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->unsignedTinyInteger('session_number'); // 1, 2, 3...
            $table->string('title', 200)->nullable(); // Optional: "Day 1: Introduction"
            
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            
            $table->string('zoom_link', 500)->nullable(); // Bisa beda per session
            $table->text('notes')->nullable(); // Catatan khusus session ini
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['batch_id', 'session_number'], 'idx_batch_session_unique');
            $table->index(['batch_id', 'start_datetime'], 'idx_batch_session_dates');
            $table->index('trainer_id', 'idx_session_trainer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_sessions');
    }
};