<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchMaterial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'batch_id',
        'title',
        'type',
        'url',
        'description',
        'uploaded_by',
        'uploaded_by_name',
    ];

    /**
     * Get the batch that owns the material
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the user who uploaded the material
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope for specific material type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific batch
     */
    public function scopeForBatch($query, int $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Get badge color based on material type
     */
    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            'pdf' => 'text-[#FF4D00] bg-orange-100',
            'video' => 'text-[#AE00FF] bg-purple-100',
            'recording' => 'text-[#AE00FF] bg-purple-100',
            'link' => 'text-[#0059FF] bg-blue-100',
            default => 'text-gray-700 bg-gray-100',
        };
    }

    /**
     * Get icon SVG based on material type
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'pdf' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" />
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                <path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>',
            'video' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-video">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>',
            'recording' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-video">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>',
            'link' => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"><path fill="#0059FF" 
                d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 
                2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 
                3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 
                1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z"/></svg>',
            default => '',
        };
    }
}