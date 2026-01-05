<?php
// BranchSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Jakarta Pusat',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bandung',
                'address' => 'Jl. Dago No. 45, Bandung',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Surabaya',
                'address' => 'Jl. Pemuda No. 78, Surabaya',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Yogyakarta',
                'address' => 'Jl. Malioboro No. 56, Yogyakarta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bali',
                'address' => 'Jl. Sunset Road No. 99, Denpasar, Bali',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('branches')->insert($branches);
    }
}