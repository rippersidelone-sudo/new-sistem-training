<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batch_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['pdf', 'video', 'recording', 'link'])->default('link');
            $table->string('url', 500);
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('uploaded_by_name')->nullable(); // Denormalized for quick access
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('batch_id');
            $table->index('type');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_materials');
    }
};