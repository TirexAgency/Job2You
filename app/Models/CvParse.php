<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvParse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'status',
        'extracted_json',
        'validated_at',
    ];

    protected $casts = [
        'extracted_json' => 'array',
        'validated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
