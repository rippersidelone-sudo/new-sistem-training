<?php

// 2024_01_01_000002_create_branches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
