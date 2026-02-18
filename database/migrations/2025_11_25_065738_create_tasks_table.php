<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->dateTime('deadline');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_id', 'is_active'], 'idx_tasks_batch_active');
            $table->index(['batch_id', 'deadline'], 'idx_tasks_batch_deadline');
            $table->index(['is_active', 'deadline'], 'idx_tasks_active_deadline');
            $table->index('deleted_at', 'idx_tasks_deleted');
        });
    }

    public function down(): void    
    {
        Schema::dropIfExists('tasks');
    }
};