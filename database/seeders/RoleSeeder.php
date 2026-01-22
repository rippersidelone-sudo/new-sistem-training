<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'HQ Admin',
                'description' => 'Pengelola pusat dan pengendali seluruh sistem',
                'access_token' => 'admin_access',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Training Coordinator',
                'description' => 'Pengatur jadwal, batch, dan peserta pelatihan',
                'access_token' => 'coordinator_access',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Trainer',
                'description' => 'Pelaksana kegiatan pelatihan',
                'access_token' => 'trainer_access',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Branch Coordinator',
                'description' => 'PIC peserta tingkat cabang',
                'access_token' => 'branch_access',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Participant',
                'description' => 'Peserta Pelatihan',
                'access_token' => null, // ← TAMBAHKAN INI
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}