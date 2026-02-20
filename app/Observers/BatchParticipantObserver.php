<?php
// app/Observers/BatchParticipantObserver.php
namespace App\Observers;

use App\Models\BatchParticipant;
use App\Models\Batch;

class BatchParticipantObserver
{
    /**
     * Handle the BatchParticipant "created" event.
     */
    public function created(BatchParticipant $participant): void
    {
        $this->updateBatchCounters($participant->batch_id);
    }

    /**
     * Handle the BatchParticipant "updated" event.
     */
    public function updated(BatchParticipant $participant): void
    {
        $this->updateBatchCounters($participant->batch_id);
    }

    /**
     * Handle the BatchParticipant "deleted" event.
     */
    public function deleted(BatchParticipant $participant): void
    {
        $this->updateBatchCounters($participant->batch_id);
    }

    /**
     * Update batch counter cache
     */
    private function updateBatchCounters(int $batchId): void
    {
        $batch = Batch::find($batchId);
        
        if ($batch) {
            // ✅ FIXED: Ganti updateCounters() jadi refreshCounters()
            $batch->refreshCounters();
        }
    }
}