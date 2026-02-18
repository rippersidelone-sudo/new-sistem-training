<?php
// database/seeders/BatchSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\BatchSession;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        $batchesData = [
            // Batch yang sudah selesai
            [
                'title' => 'Python Fundamentals - Batch 1',
                'category_id' => $categories->where('name', 'Python Fundamentals')->first()?->id ?? 1,
                'trainer_id' => $trainers->random()->id,
                'start_date' => Carbon::now()->subMonths(3)->startOfMonth(),
                'days' => 5, // Durasi 5 hari
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
                'days' => 10, // Durasi 10 hari
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
                'days' => 15, // Durasi 15 hari (5 hari sudah lewat, 10 hari lagi)
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
                'days' => 10, // Durasi 10 hari
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
                'days' => 14, // Durasi 14 hari
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
                'days' => 5, // Durasi 5 hari
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
                'days' => 7, // Durasi 7 hari
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
                'days' => 14, // Durasi 14 hari
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
                'days' => 14, // Durasi 14 hari
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
                'days' => 14, // Durasi 14 hari
                'zoom_link' => 'https://zoom.us/j/0123456789',
                'min_quota' => 5,
                'max_quota' => 12,
                'status' => 'Scheduled',
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($batchesData as $batchData) {
                // Extract days dan buat sessions
                $days = $batchData['days'];
                $startDate = $batchData['start_date'];
                unset($batchData['days']);
                
                // Calculate end_date from sessions
                $endDate = $startDate->copy()->addDays($days - 1)->setTime(16, 0, 0);
                $batchData['end_date'] = $endDate;
                
                // Create batch
                $batch = Batch::create($batchData);
                
                // Create sessions for each day
                $currentDate = $startDate->copy();
                for ($i = 1; $i <= $days; $i++) {
                    // Random trainer from trainers collection
                    $sessionTrainer = $trainers->random();
                    
                    // Random start time between 08:00 - 09:00
                    $startHour = rand(8, 9);
                    $startMinute = $startHour === 9 ? 0 : (rand(0, 1) * 30); // 08:00, 08:30, atau 09:00
                    
                    // Duration 6-8 hours
                    $durationHours = rand(6, 8);
                    
                    $sessionStart = $currentDate->copy()->setTime($startHour, $startMinute, 0);
                    $sessionEnd = $sessionStart->copy()->addHours($durationHours);
                    
                    // Optional: zoom link khusus untuk beberapa session (20% chance)
                    $customZoomLink = (rand(1, 100) <= 20) ? 'https://zoom.us/j/' . rand(1000000000, 9999999999) : null;
                    
                    // Optional: title untuk beberapa session (30% chance)
                    $titles = [
                        'Introduction & Setup',
                        'Core Concepts',
                        'Hands-on Practice',
                        'Advanced Topics',
                        'Project Work',
                        'Review & Q&A',
                        'Final Project',
                    ];
                    $sessionTitle = (rand(1, 100) <= 30) ? $titles[array_rand($titles)] : null;
                    
                    BatchSession::create([
                        'batch_id' => $batch->id,
                        'trainer_id' => $sessionTrainer->id,
                        'session_number' => $i,
                        'title' => $sessionTitle,
                        'start_datetime' => $sessionStart,
                        'end_datetime' => $sessionEnd,
                        'zoom_link' => $customZoomLink,
                        'notes' => null,
                    ]);
                    
                    // Move to next day
                    $currentDate->addDay();
                }
                
                $this->command->info("Created batch: {$batch->title} with {$days} sessions");
            }
            
            DB::commit();
            $this->command->info('All batches and sessions seeded successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding batches: ' . $e->getMessage());
        }
    }
}