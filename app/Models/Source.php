<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_url',
        'collector_key',
        'last_run_at',
    ];

    protected $casts = [
        'last_run_at' => 'datetime',
    ];

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }
}
