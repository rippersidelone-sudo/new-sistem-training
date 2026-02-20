<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->unsignedTinyInteger('rating')->default(0);
            $table->timestamps();

            $table->unique(['batch_id', 'user_id'], 'feedback_unique');
            $table->index(['batch_id', 'rating'], 'idx_feedback_batch_rating');
            $table->index('rating', 'idx_feedback_rating');
            $table->index('created_at', 'idx_feedback_created');
        });

        DB::statement('ALTER TABLE feedback ADD CONSTRAINT rating_check CHECK (rating >= 0 AND rating <= 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};