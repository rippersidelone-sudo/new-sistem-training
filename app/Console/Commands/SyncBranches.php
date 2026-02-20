<?php

namespace App\Console\Commands;

use App\Services\ExternalAPI\BranchSyncService;
use Illuminate\Console\Command;

class SyncBranches extends Command
{
    protected $signature   = 'sync:branches';
    protected $description = 'Sync data branches dari External API';

    public function __construct(private BranchSyncService $syncService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Memulai sync branches dari External API...');

        $result = $this->syncService->sync();

        if (!$result['success']) {
            $this->error('Sync gagal: ' . $result['message']);
            return self::FAILURE;
        }

        $this->info($result['message']);

        if ($result['errors'] > 0) {
            $this->warn('Ada ' . $result['errors'] . ' branch yang gagal di-sync. Cek log untuk detail.');
        }

        return self::SUCCESS;
    }
}