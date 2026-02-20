<?php

namespace App\Console\Commands;

use App\Services\ExternalAPI\ParticipantSyncService;
use Illuminate\Console\Command;

class SyncParticipants extends Command
{
    protected $signature   = 'sync:participants';
    protected $description = 'Sync data participants dari External API';

    public function __construct(private ParticipantSyncService $syncService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Memulai sync participants dari External API...');

        $result = $this->syncService->sync();

        if (!$result['success']) {
            $this->error('Sync gagal: ' . $result['message']);
            return self::FAILURE;
        }

        $this->info($result['message']);

        if ($result['errors'] > 0) {
            $this->warn('Ada ' . $result['errors'] . ' participant yang gagal di-sync. Cek log untuk detail.');
        }

        return self::SUCCESS;
    }
}