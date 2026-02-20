<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // API Sync Fields
            $table->string('external_id')->nullable()->unique()
                ->comment('skill_id dari External API');

            $table->string('name')->unique();
            $table->text('description')->nullable();

            // tracking kapan terakhir kali di-sync
            $table->timestamp('last_synced_at')->nullable()
                ->comment('Waktu terakhir data ini di-sync dari API');

            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('external_id', 'idx_categories_external_id');
            $table->index('last_synced_at', 'idx_categories_last_synced');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};