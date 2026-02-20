<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['batch_id', 'user_id']);
            $table->index(['batch_id', 'status'], 'idx_bp_batch_status');
            $table->index(['user_id', 'status'], 'idx_bp_user_status');
            $table->index('approved_by', 'idx_bp_approved_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_participants');
    }
};