<?php

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
                'role_id'           => 1,
                'branch_id'         => null,
                'name'              => 'Admin Pusat',
                'username'          => 'admin',
                'email'             => 'admin@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password'          => Hash::make('password123'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            // Training Coordinator
            [
                'role_id'           => 2,
                'branch_id'         => null,
                'name'              => 'Coordinator Utama',
                'username'          => 'coordinator',
                'email'             => 'coordinator@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password'          => Hash::make('password123'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            // Trainer
            [
                'role_id'           => 3,
                'branch_id'         => null,
                'name'              => 'Alldi Ramadhan',
                'username'          => 'trainer.alldi',
                'email'             => 'alldi@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password'          => Hash::make('password123'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            // participant untuk tes
            [
                'role_id'           => 5,
                'branch_id'         => 1,
                'name'              => 'Aryo Wijaya',
                'username'          => 'A.wijaya',
                'email'             => 'aryo@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password'          => Hash::make('password123'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}