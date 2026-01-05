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
        // Insert Categories
        $categories = [
            [
                'id' => 1,
                'name' => 'Python Basic',
                'description' => 'Pengenalan dasar-dasar pemrograman Python',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Python Intermediate',
                'description' => 'Pemrograman Python tingkat menengah',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'name' => 'Python Game Developer',
                'description' => 'Pengembangan game menggunakan Python',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'name' => 'Web Development with Laravel',
                'description' => 'Pengembangan web menggunakan framework Laravel',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => 'JavaScript Fundamentals',
                'description' => 'Dasar-dasar pemrograman JavaScript',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('categories')->insert($categories);

        // Insert Prerequisites
        // Python Game Developer requires Python Basic & Python Intermediate
        $prerequisites = [
            [
                'category_id' => 3, // Python Game Developer
                'prerequisite_id' => 1, // Python Basic
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => 3, // Python Game Developer
                'prerequisite_id' => 2, // Python Intermediate
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Python Intermediate requires Python Basic
            [
                'category_id' => 2, // Python Intermediate
                'prerequisite_id' => 1, // Python Basic
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('category_prerequisites')->insert($prerequisites);
    }
}
