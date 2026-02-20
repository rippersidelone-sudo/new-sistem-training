<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    // ============================================================
    // FILLABLE & CASTS
    // ============================================================

    protected $fillable = [
        'batch_id',
        'user_id',
        'attendance_date',
        'checkin_time',
        'checkout_time',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'checkin_time' => 'string',
            'checkout_time' => 'string',
        ];
    }

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'Checked-in');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'Absent');
    }

    public function scopeForBatch($query, int $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', now()->toDateString());
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

    public function isCheckedIn(): bool
    {
        return $this->status === 'Checked-in';
    }

    public function isAbsent(): bool
    {
        return $this->status === 'Absent';
    }

    public function getDurationInMinutes(): ?int
    {
        if (!$this->checkin_time || !$this->checkout_time) {
            return null;
        }

        return $this->checkin_time->diffInMinutes($this->checkout_time);
    }
}