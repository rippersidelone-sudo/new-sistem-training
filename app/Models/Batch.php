<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Batch extends Model
{
    use SoftDeletes, LogsActivity;

    // ============================================================
    // FILLABLE & CASTS
    // ============================================================

    protected $fillable = [
        'title',
        'category_id',
        'trainer_id',
        'start_date',
        'end_date',
        'zoom_link',
        'min_quota',
        'max_quota',
        'status',
        // Counter cache
        'participants_count',
        'passed_count',
        'pending_count',
        'failed_count',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'min_quota' => 'integer',
            'max_quota' => 'integer',
            'participants_count' => 'integer',
            'passed_count' => 'integer',
            'pending_count' => 'integer',
            'failed_count' => 'integer',
        ];
    }

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'batch_participants')
            ->withPivot('approved_by', 'status', 'rejection_reason')
            ->withTimestamps();
    }

    public function batchParticipants(): HasMany
    {
        return $this->hasMany(BatchParticipant::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(BatchMaterial::class);
    }

    /**
     * Batch Sessions (NEW)
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(BatchSession::class)->orderBy('session_number');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'Scheduled');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'Ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeForTrainer($query, int $trainerId)
    {
        return $query->where('trainer_id', $trainerId);
    }

    public function scopeForCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeHasAvailableSlot($query)
    {
        return $query->whereRaw('participants_count < max_quota');
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if (!$keyword) return $query;

        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhereHas('trainer', fn($t) => $t->where('name', 'like', "%{$keyword}%"))
              ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$keyword}%"));
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }
    
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function scopeActiveToday($query)
    {
        $today = now()->toDateString();
        return $query->whereDate('start_date', '<=', $today)
                     ->whereDate('end_date', '>=', $today);
    }

    // ============================================================
    // COUNTER CACHE — UPDATE METHODS
    // ============================================================

    public function refreshCounters(): void
    {
        if (!$this->exists) {
            return;
        }

        $counters = DB::table('batch_participants')
            ->where('batch_id', $this->id)
            ->selectRaw('
                COUNT(CASE WHEN status = "Approved" THEN 1 END) as approved_count,
                COUNT(CASE WHEN status = "Pending" THEN 1 END) as pending_count
            ')
            ->first();

        $this->participants_count = $counters->approved_count ?? 0;
        $this->pending_count = $counters->pending_count ?? 0;

        $this->passed_count = DB::table('batch_participants as bp')
            ->where('bp.batch_id', $this->id)
            ->where('bp.status', 'Approved')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('attendances as a')
                  ->whereColumn('a.user_id', 'bp.user_id')
                  ->where('a.batch_id', $this->id)
                  ->where('a.status', 'Approved');
            })
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('feedback as f')
                  ->whereColumn('f.user_id', 'bp.user_id')
                  ->where('f.batch_id', $this->id);
            })
            ->count();

        $this->failed_count = max(0, $this->participants_count - $this->passed_count);
        $this->saveQuietly();
    }

    public function refreshParticipantsCount(): void
    {
        if (!$this->exists) {
            return;
        }

        $this->participants_count = $this->batchParticipants()
            ->where('status', 'Approved')
            ->count();

        $this->saveQuietly();
    }

    public function refreshPendingCount(): void
    {
        if (!$this->exists) {
            return;
        }

        $this->pending_count = $this->batchParticipants()
            ->where('status', 'Pending')
            ->count();

        $this->saveQuietly();
    }

    // ============================================================
    // HELPER METHODS - QUOTA
    // ============================================================

    public function hasAvailableSlot(): bool
    {
        return $this->participants_count < $this->max_quota;
    }

    public function isFull(): bool
    {
        return $this->participants_count >= $this->max_quota;
    }

    public function getPassRate(): float
    {
        if ($this->participants_count === 0) return 0.0;
        return round(($this->passed_count / $this->participants_count) * 100, 1);
    }

    // ============================================================
    // HELPER METHODS - DATE & TIME
    // ============================================================

    public function isOngoing(): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    public function hasStarted(): bool
    {
        return $this->start_date <= now();
    }

    public function hasEnded(): bool
    {
        return $this->end_date < now();
    }

    public function getDaysUntilStart(): int
    {
        if ($this->hasStarted()) {
            return 0;
        }
        return now()->diffInDays($this->start_date, false);
    }

    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    // ============================================================
    // HELPER METHODS - SESSIONS (NEW)
    // ============================================================

    /**
     * Check if batch has multiple sessions
     */
    public function hasMultipleSessions(): bool
    {
        return $this->sessions()->count() > 1;
    }

    /**
     * Get total number of sessions
     */
    public function getSessionsCount(): int
    {
        return $this->sessions()->count();
    }

    /**
     * Get first session
     */
    public function getFirstSession(): ?BatchSession
    {
        return $this->sessions()->orderBy('session_number')->first();
    }

    /**
     * Get last session
     */
    public function getLastSession(): ?BatchSession
    {
        return $this->sessions()->orderBy('session_number', 'desc')->first();
    }

    /**
     * Get date range summary for display
     * Example: "15 Feb - 17 Feb 2025"
     */
    public function getDateRangeSummary(): string
    {
        $first = $this->getFirstSession();
        $last = $this->getLastSession();
        
        if (!$first || !$last) {
            return formatDate($this->start_date);
        }
        
        // Same day
        if ($first->start_datetime->isSameDay($last->end_datetime)) {
            return formatDate($first->start_datetime, 'd M Y');
        }
        
        // Different days
        return formatDate($first->start_datetime, 'd M') . ' - ' . formatDate($last->end_datetime, 'd M Y');
    }

    /**
     * Get trainers summary for display
     * Example: "John Doe" or "2 Trainers"
     */
    public function getTrainersSummary(): string
    {
        $trainers = $this->sessions()
            ->with('trainer')
            ->get()
            ->pluck('trainer.name')
            ->unique()
            ->filter();
        
        $count = $trainers->count();
        
        if ($count === 0) {
            return $this->trainer->name ?? 'No Trainer';
        }
        
        if ($count === 1) {
            return $trainers->first();
        }
        
        return $count . ' Trainers';
    }

    /**
     * Get all unique trainers from sessions
     */
    public function getAllTrainers()
    {
        return $this->sessions()
            ->with('trainer')
            ->get()
            ->pluck('trainer')
            ->unique('id')
            ->filter();
    }

    /**
     * Get total duration in hours from all sessions
     */
    public function getTotalDurationInHours(): float
    {
        $totalMinutes = $this->sessions()
            ->get()
            ->sum(function($session) {
                return $session->getDurationInMinutes();
            });
        
        return round($totalMinutes / 60, 1);
    }

    /**
     * Get sessions grouped by date
     */
    public function getSessionsByDate()
    {
        return $this->sessions()
            ->get()
            ->groupBy(function($session) {
                return $session->start_datetime->format('Y-m-d');
            });
    }

    /**
     * Check if batch has sessions today
     */
    public function hasSessionToday(): bool
    {
        return $this->sessions()
            ->whereDate('start_datetime', now()->toDateString())
            ->exists();
    }

    /**
     * Get upcoming sessions
     */
    public function getUpcomingSessions()
    {
        return $this->sessions()
            ->where('start_datetime', '>', now())
            ->orderBy('start_datetime')
            ->get();
    }

    /**
     * Get completed sessions
     */
    public function getCompletedSessions()
    {
        return $this->sessions()
            ->where('end_datetime', '<', now())
            ->orderBy('start_datetime')
            ->get();
    }

    /**
     * Get current ongoing session (if any)
     */
    public function getCurrentSession(): ?BatchSession
    {
        $now = now();
        return $this->sessions()
            ->where('start_datetime', '<=', $now)
            ->where('end_datetime', '>=', $now)
            ->first();
    }

    // ============================================================
    // ACTIVITY LOG
    // ============================================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'trainer_id', 'start_date', 'end_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} batch");
    }
}