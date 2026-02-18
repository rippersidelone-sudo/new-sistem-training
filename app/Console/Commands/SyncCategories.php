<?php

namespace App\Console\Commands;

use App\Services\ExternalAPI\CategorySyncService;
use Illuminate\Console\Command;

class SyncCategories extends Command
{
    protected $signature   = 'sync:categories';
    protected $description = 'Sync data categories dari External API';

    public function __construct(private CategorySyncService $syncService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Memulai sync categories dari External API...');

        $result = $this->syncService->sync();

        if (!$result['success']) {
            $this->error('Sync gagal: ' . $result['message']);
            return self::FAILURE;
        }

        $this->info($result['message']);

        if ($result['errors'] > 0) {
            $this->warn('Ada ' . $result['errors'] . ' category yang gagal di-sync. Cek log untuk detail.');
        }

        return self::SUCCESS;
    }
}