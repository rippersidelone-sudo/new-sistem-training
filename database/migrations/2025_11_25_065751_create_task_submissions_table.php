<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('file_path', 500);
            $table->text('notes')->nullable();

            $table->enum('status', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            $table->text('feedback')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->unique(['task_id', 'user_id']);
            $table->index('status');
            $table->index(['task_id', 'status'], 'idx_task_submissions_task_status');
            $table->index('reviewed_by', 'idx_task_submissions_reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
