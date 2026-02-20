<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('external_id')->nullable()->unique()
                ->comment('teacher_id dari External API (hanya untuk Participant)');

            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            
            // Username: wajib untuk semua user, unique
            $table->string('username')->unique()
                ->comment('Username untuk login. Participant dari API, role lain di-generate otomatis');
            
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->timestamp('last_synced_at')->nullable()
                ->comment('Waktu terakhir data ini di-sync dari API (khusus Participant)');

            $table->timestamps();
            $table->softDeletes();

            $table->index('username');
            $table->index('email');
            $table->index('role_id');
            $table->index('branch_id');
            $table->index('external_id', 'idx_users_external_id');
            $table->index('last_synced_at', 'idx_users_last_synced');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};