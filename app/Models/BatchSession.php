<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BatchSession extends Model
{
    protected $fillable = [
        'batch_id',
        'trainer_id',
        'session_number',
        'title',
        'start_datetime',
        'end_datetime',
        'zoom_link',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'session_number' => 'integer',
        ];
    }

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeForBatch($query, int $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    public function scopeOrderBySession($query)
    {
        return $query->orderBy('session_number', 'asc');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_datetime', now()->toDateString());
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    public function getFormattedDateAttribute(): string
    {
        return formatDate($this->start_datetime, 'd M Y');
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        return $this->start_datetime->format('H:i') . ' - ' . $this->end_datetime->format('H:i');
    }

    public function getDurationInMinutes(): int
    {
        return $this->start_datetime->diffInMinutes($this->end_datetime);
    }

    public function getDurationInHours(): float
    {
        return round($this->getDurationInMinutes() / 60, 1);
    }

    public function isToday(): bool
    {
        return $this->start_datetime->isToday();
    }

    public function hasStarted(): bool
    {
        return $this->start_datetime <= now();
    }

    public function hasEnded(): bool
    {
        return $this->end_datetime < now();
    }

    public function isOngoing(): bool
    {
        $now = now();
        return $this->start_datetime <= $now && $this->end_datetime >= $now;
    }
}