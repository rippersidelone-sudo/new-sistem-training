<?php
// app/Models/TaskSubmission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'batch_id',
        'user_id',
        'file_path',
        'status',
    ];

    /**
     * Get the task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the user (participant)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accept submission
     */
    public function accept()
    {
        $this->update(['status' => 'Accepted']);
    }

    /**
     * Reject submission
     */
    public function reject()
    {
        $this->update(['status' => 'Rejected']);
    }

    /**
     * Scope for pending submissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for accepted submissions
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'Accepted');
    }

    /**
     * Scope for rejected submissions
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }
}

