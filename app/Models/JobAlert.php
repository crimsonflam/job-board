<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAlert extends Model
{
    protected $fillable = [
        'user_id', 'keyword', 'category_id', 'location', 'type',
        'is_remote', 'frequency', 'is_active', 'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'is_remote' => 'boolean',
            'is_active' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
