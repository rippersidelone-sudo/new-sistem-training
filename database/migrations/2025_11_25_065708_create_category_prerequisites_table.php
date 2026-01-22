<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('prerequisite_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['category_id', 'prerequisite_id'], 'category_prerequisite_unique');
            
            $table->index('category_id');
            $table->index('prerequisite_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_prerequisites');
    }
};
