<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    // Prerequisites that this category requires
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_prerequisites',
            'category_id',
            'prerequisite_id'
        )->withTimestamps();
    }

    // Categories that require this category as a prerequisite
    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_prerequisites',
            'prerequisite_id',
            'category_id'
        )->withTimestamps();
    }
}
