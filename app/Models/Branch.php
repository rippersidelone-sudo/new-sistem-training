<?php
// app/Models/Branch.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id',  
        'code',         
        'name',
        'address',
        'contact',      
        'last_synced_at', 
    ];

    /**
     * Users belong to this branch
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Batches associated with this branch
     * (Assuming batches table has branch_id column)
     * If not, you can remove this relation
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Get participants (users with Participant role) in this branch
     */
    public function participants(): HasMany
    {
        return $this->hasMany(User::class)
            ->whereHas('role', function($query) {
                $query->where('name', 'Participant');
            });
    }
}