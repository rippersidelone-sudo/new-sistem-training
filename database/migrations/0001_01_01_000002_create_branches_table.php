<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            $table->string('external_id')->nullable()->unique()
                ->comment('branch_id dari External API');
            $table->string('code', 50)->nullable()
                ->comment('branch_code dari External API');

            $table->string('name')->unique(); // branch_name
            $table->string('type', 50)->nullable();
            $table->string('category', 100)->nullable();

            $table->string('country', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();

            $table->string('address')->nullable();
            $table->string('contact', 100)->nullable()->comment('contact dari External API');

            $table->timestamp('last_synced_at')->nullable()
                ->comment('Waktu terakhir data ini di-sync dari API');

            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('external_id', 'idx_branches_external_id');
            $table->index('last_synced_at', 'idx_branches_last_synced');
            $table->index(['country','province','city'], 'idx_branches_location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
