<?php
// database/seeders/BatchSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil kategori yang sudah ada
        $categories = Category::all();
        
        // Ambil user dengan role Trainer (role_id = 3)
        $trainers = User::where('role_id', 3)->get();

        if ($categories->isEmpty() || $trainers->isEmpty()) {
            $this->command->warn('Please run CategorySeeder and UserSeeder first!');
            return;
        }

        $batches = [
            // Batch yang sudah selesai
            [
                'title' => 'Python Fundamentals - Batch 1',
                'category_id' => $categories->where('name', 'Python Fundamentals')->first()?->id ?? 1,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->subMonths(3)->startOfMonth(),
                'end_date' => Carbon::now()->subMonths(3)->addDays(5),
                'zoom_link' => 'https://zoom.us/j/1234567890',
                'min_quota' => 5,
                'max_quota' => 15,
                'status' => 'Completed',
            ],
            [
                'title' => 'Web Development Basics - Batch 1',
                'category_id' => $categories->where('name', 'Web Development Basics')->first()?->id ?? 2,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->subMonths(2)->startOfMonth(),
                'end_date' => Carbon::now()->subMonths(2)->addDays(10),
                'zoom_link' => 'https://zoom.us/j/2345678901',
                'min_quota' => 8,
                'max_quota' => 20,
                'status' => 'Completed',
            ],

            // Batch yang sedang berlangsung
            [
                'title' => 'Python Game Development - Batch 1',
                'category_id' => $categories->where('name', 'Python Game Development')->first()?->id ?? 3,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(10),
                'zoom_link' => 'https://zoom.us/j/3456789012',
                'min_quota' => 5,
                'max_quota' => 12,
                'status' => 'Ongoing',
            ],
            [
                'title' => 'JavaScript Advanced - Batch 2',
                'category_id' => $categories->where('name', 'JavaScript Advanced')->first()?->id ?? 4,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(7),
                'zoom_link' => 'https://zoom.us/j/4567890123',
                'min_quota' => 10,
                'max_quota' => 25,
                'status' => 'Ongoing',
            ],

            // Batch yang dijadwalkan
            [
                'title' => 'React Framework - Batch 1',
                'category_id' => $categories->where('name', 'React Framework')->first()?->id ?? 5,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(21),
                'zoom_link' => 'https://zoom.us/j/5678901234',
                'min_quota' => 8,
                'max_quota' => 15,
                'status' => 'Scheduled',
            ],
            [
                'title' => 'Python Fundamentals - Batch 2',
                'category_id' => $categories->where('name', 'Python Fundamentals')->first()?->id ?? 1,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(19),
                'zoom_link' => 'https://zoom.us/j/6789012345',
                'min_quota' => 5,
                'max_quota' => 20,
                'status' => 'Scheduled',
            ],
            [
                'title' => 'Database Design - Batch 1',
                'category_id' => $categories->where('name', 'Database Design')->first()?->id ?? 6,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(28),
                'zoom_link' => 'https://zoom.us/j/7890123456',
                'min_quota' => 6,
                'max_quota' => 18,
                'status' => 'Scheduled',
            ],
            [
                'title' => 'Laravel Framework - Batch 1',
                'category_id' => $categories->where('name', 'Laravel Framework')->first()?->id ?? 7,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->addMonth()->startOfMonth(),
                'end_date' => Carbon::now()->addMonth()->addDays(14),
                'zoom_link' => 'https://zoom.us/j/8901234567',
                'min_quota' => 10,
                'max_quota' => 20,
                'status' => 'Scheduled',
            ],
            [
                'title' => 'Mobile Development with Flutter - Batch 1',
                'category_id' => $categories->where('name', 'Mobile Development')->first()?->id ?? 8,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->addMonth()->addDays(7),
                'end_date' => Carbon::now()->addMonth()->addDays(21),
                'zoom_link' => 'https://zoom.us/j/9012345678',
                'min_quota' => 8,
                'max_quota' => 16,
                'status' => 'Scheduled',
            ],
            [
                'title' => 'Data Science with Python - Batch 1',
                'category_id' => $categories->where('name', 'Data Science')->first()?->id ?? 9,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->addMonth()->addDays(15),
                'end_date' => Carbon::now()->addMonth()->addDays(29),
                'zoom_link' => 'https://zoom.us/j/0123456789',
                'min_quota' => 5,
                'max_quota' => 12,
                'status' => 'Scheduled',
            ],
        ];

        foreach ($batches as $batch) {
            Batch::create($batch);
        }

        $this->command->info('Batches seeded successfully!');
    }
}