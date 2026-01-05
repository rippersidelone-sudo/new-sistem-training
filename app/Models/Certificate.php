<?php

// app/Models/Certificate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'user_id',
        'file_path',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    /**
     * Get the batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the user (participant)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate certificate number
     */
    public static function generateCertificateNumber($batchCode, $userId)
    {
        return sprintf(
            'CERT-%s-%s-%s',
            $batchCode,
            str_pad($userId, 4, '0', STR_PAD_LEFT),
            now()->format('Ymd')
        );
    }

    /**
     * Boot method to auto-generate certificate number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->issued_at)) {
                $certificate->issued_at = now();
            }
        });
    }
}