<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->enum('type', ['pdf', 'video', 'recording', 'link'])->default('link');
            $table->string('url', 1000);
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('uploaded_by_name', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['batch_id', 'type'], 'idx_bm_batch_type');
            $table->index(['batch_id', 'deleted_at'], 'idx_bm_batch_deleted');
            $table->index('uploaded_by', 'idx_bm_uploaded_by');
            $table->index(['batch_id', 'created_at'], 'idx_bm_batch_created');
            $table->index('deleted_at', 'idx_bm_deleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_materials');
    }
};