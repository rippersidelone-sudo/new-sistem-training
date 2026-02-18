<?php

// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'title',
        'description',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get all submissions
     */
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    /**
     * Get pending submissions
     */
    public function pendingSubmissions()
    {
        return $this->submissions()->where('status', 'Pending');
    }

    /**
     * Get accepted submissions
     */
    public function acceptedSubmissions()
    {
        return $this->submissions()->where('status', 'Accepted');
    }

    /**
     * Check if deadline has passed
     */
    public function isOverdue()
    {
        return $this->deadline < now();
    }

    /**
     * Check if user has submitted
     */
    public function hasSubmission(User $user)
    {
        return $this->submissions()->where('user_id', $user->id)->exists();
    }
}
