<?php
// UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // HQ Admin
            [
                'role_id' => 1, // HQ Admin
                'branch_id' => null,
                'name' => 'Admin Pusat',
                'email' => 'admin@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Training Coordinator
            [
                'role_id' => 2, // Training Coordinator
                'branch_id' => null,
                'name' => 'Coordinator Utama',
                'email' => 'coordinator@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Trainer
            [
                'role_id' => 3, // Trainer
                'branch_id' => null,
                'name' => 'Budi Santoso',
                'email' => 'trainer1@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Branch Coordinator - Jakarta
            [
                'role_id' => 4, // Branch Coordinator
                'branch_id' => 1, // Jakarta Pusat
                'name' => 'Siti Nurhaliza',
                'email' => 'jakarta@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Branch Coordinator - Bandung
            [
                'role_id' => 4, // Branch Coordinator
                'branch_id' => 2, // Bandung
                'name' => 'Dedi Mulyadi',
                'email' => 'bandung@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Participant - Jakarta
            [
                'role_id' => 5, // Participant
                'branch_id' => 1, // Jakarta Pusat
                'name' => 'Andi Wijaya',
                'email' => 'andi@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}