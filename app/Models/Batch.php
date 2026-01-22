<?php
// app/Models/Batch.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Batch extends Model
{
    use SoftDeletes;

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
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'min_quota' => 'integer',
            'max_quota' => 'integer',
        ];
    }

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

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
    public function materials(): HasMany
    {
        return $this->hasMany(BatchMaterial::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'trainer_id', 'start_date', 'end_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} batch");
    }
}


