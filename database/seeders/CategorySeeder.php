<?php
// CategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Insert Categories - SESUAIKAN DENGAN BatchSeeder
        $categories = [
            [
                'id' => 1,
                'name' => 'Python Fundamentals', // ← DIUBAH
                'description' => 'Pengenalan dasar-dasar pemrograman Python',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Web Development Basics', // ← DIUBAH
                'description' => 'Dasar-dasar pengembangan web',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'name' => 'Python Game Development', // ← DIUBAH
                'description' => 'Pengembangan game menggunakan Python',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'name' => 'JavaScript Advanced', // ← DIUBAH
                'description' => 'JavaScript tingkat lanjut',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => 'React Framework', // ← DIUBAH
                'description' => 'Pengembangan web menggunakan React',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // TAMBAHKAN KATEGORI BARU
            [
                'id' => 6,
                'name' => 'Database Design',
                'description' => 'Desain dan manajemen database',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'name' => 'Laravel Framework',
                'description' => 'Pengembangan web menggunakan framework Laravel',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'name' => 'Mobile Development',
                'description' => 'Pengembangan aplikasi mobile dengan Flutter',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 9,
                'name' => 'Data Science',
                'description' => 'Ilmu data menggunakan Python',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('categories')->insert($categories);

        // Update Prerequisites
        $prerequisites = [
            [
                'category_id' => 3, // Python Game Development
                'prerequisite_id' => 1, // Python Fundamentals
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => 7, // Laravel Framework
                'prerequisite_id' => 2, // Web Development Basics
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('category_prerequisites')->insert($prerequisites);
    }
}