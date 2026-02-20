<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;

class ExternalBranchAccountsSeeder extends Seeder
{
    /**
     * Buat akun Branch Coordinator otomatis untuk setiap branch yang sudah ada di DB.
     */
    public function run(): void
    {
        $role = Role::where('name', 'Branch Coordinator')->first();

        if (!$role) {
            $this->command->error('Role "Branch Coordinator" not found. Jalankan RoleSeeder dulu.');
            return;
        }

        $branches = Branch::query()
            ->orderBy('id')
            ->get();

        if ($branches->isEmpty()) {
            $this->command->warn('Tidak ada branch di database. Jalankan sync branch dari API dulu atau BranchSeeder.');
            return;
        }

        // Password default untuk semua PIC 
        $defaultPassword = 'password123';

        $created = 0;
        $skipped = 0;

        foreach ($branches as $branch) {
            // Buat "slug" aman untuk dipakai di email
            $code = $branch->code ?: $branch->external_id ?: (string) $branch->id;
            $safeCode = Str::slug((string) $code, '.'); // misal: "JKT-01" -> "jkt-01"
            $safeCode = str_replace('-', '.', $safeCode);

            // Email unik dan konsisten
            $email = "pic.{$safeCode}@branch.local";

            // Jika email sudah ada, skip
            if (User::where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            // Nama user PIC
            $displayName = 'PIC ' . ($branch->name ?? 'Branch');

            User::create([
                'role_id'            => $role->id,
                'branch_id'          => $branch->id,
                'name'               => $displayName,
                'username'           => 'pic.' . $safeCode,
                'email'              => $email,
                'phone'              => null,
                'password'           => $defaultPassword,
                'email_verified_at'  => now(),
            ]);

            $created++;
        }

        $this->command->info("ExternalBranchAccountsSeeder done. Created: {$created}, Skipped(existing): {$skipped}");
        $this->command->warn("Default password PIC: {$defaultPassword}");
        $this->command->warn("Contoh login: pic.<branch_code>@branch.local");
    }
}
