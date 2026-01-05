<?php
// 2025_11_25_065758_create_feedback_table.php

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
            
            // User can only give feedback once per batch
            $table->unique(['batch_id', 'user_id'], 'feedback_unique');
            
            $table->index('batch_id');
            $table->index('user_id');
            $table->index('rating');
        });

        // Add check constraint using raw SQL
        DB::statement('ALTER TABLE feedback ADD CONSTRAINT rating_check CHECK (rating >= 0 AND rating <= 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};