<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brief',
        'result',
        'provider',
        'model',
        'total_hours',
        'price_low',
        'price_high',
        'currency',
        'hourly_rate',
    ];

    protected $casts = [
        'result' => 'array',
        'total_hours' => 'decimal:2',
        'price_low' => 'decimal:2',
        'price_high' => 'decimal:2',
    ];

    public function getTasksAttribute()
    {
        return $this->result['tasks'] ?? [];
    }

    public function getNotesAttribute()
    {
        return $this->result['notes'] ?? '';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
