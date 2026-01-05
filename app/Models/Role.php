<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'access_token',
    ];

    protected $hidden = [
        'access_token',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
