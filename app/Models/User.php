<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id',
        'branch_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Batches where user is the trainer
    public function trainedBatches(): HasMany
    {
        return $this->hasMany(Batch::class, 'trainer_id');
    }

    // Batches where user is a participant
    public function participatingBatches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'batch_participants')
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

    public function taskSubmissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    // Participants approved by this user
    public function approvedParticipants(): HasMany
    {
        return $this->hasMany(BatchParticipant::class, 'approved_by');
    }

    // Submissions reviewed by this user
    public function reviewedSubmissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class, 'reviewed_by');
    }

    // Certificates issued by this user
    public function issuedCertificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'issued_by');
    }
}

