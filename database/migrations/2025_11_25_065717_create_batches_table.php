<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->string('zoom_link', 500)->nullable();

            $table->unsignedSmallInteger('min_quota')->default(0);
            $table->unsignedSmallInteger('max_quota')->default(10);
            $table->enum('status', ['Scheduled', 'Ongoing', 'Completed'])->default('Scheduled');

            // Counter Cache Columns
            $table->unsignedInteger('participants_count')->default(0)->comment('Total approved participants');
            $table->unsignedInteger('passed_count')->default(0)->comment('Total participants yang lulus (hadir + feedback)');
            $table->unsignedInteger('pending_count')->default(0)->comment('Total pending approval');
            $table->unsignedInteger('failed_count')->default(0)->comment('Total participants yang tidak lulus');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            // 1. Status + date range → Dashboard, Reports, Calendar
            $table->index(['status', 'start_date', 'end_date'], 'idx_batches_status_dates');

            // 2. Trainer + status → Trainer dashboard (My Batches)
            $table->index(['trainer_id', 'status'], 'idx_batches_trainer_status');

            // 3. Category + status → Category filtering
            $table->index(['category_id', 'status'], 'idx_batches_category_status');

            // 4. Status + created_at → Dashboard sorting
            $table->index(['status', 'created_at'], 'idx_batches_status_created');

            // 5. Soft delete + status → Laravel default scope (deleted_at IS NULL)
            $table->index(['deleted_at', 'status'], 'idx_batches_deleted_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};