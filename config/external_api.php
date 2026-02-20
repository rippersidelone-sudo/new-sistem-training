<?php

return [
    /*
    |--------------------------------------------------------------------------
    | External API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi dengan External API (Branches API v2.0.0)
    | API Key disimpan di .env untuk keamanan - JANGAN hardcode di sini!
    |
    */

    'base_url' => env('EXTERNAL_API_URL', 'https://branches-api.onrender.com'),

    'api_key' => env('EXTERNAL_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Request Settings
    |--------------------------------------------------------------------------
    */

    // Timeout dalam detik untuk setiap request ke API
    'timeout' => env('EXTERNAL_API_TIMEOUT', 30),

    // Retry attempts jika API gagal
    'retry_attempts' => 3,

    // Delay antar retry dalam milliseconds
    'retry_delay' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Endpoints
    |--------------------------------------------------------------------------
    */

    'endpoints' => [
        'branches'         => '/api/branches',
        'teachers'         => '/api/teachers',
        'branch_teachers'  => '/api/branches/{branch_id}/teachers',
        'skills'           => '/api/skills',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sync Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk proses sinkronisasi data dari API
    |
    */

    // Role ID untuk Participant di sistem kita
    // Sesuaikan dengan role_id di tabel roles
    'participant_role_id' => env('PARTICIPANT_ROLE_ID', 5),

    // Batch size saat sync (berapa data diproses sekaligus)
    'sync_batch_size' => 100,
];