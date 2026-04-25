<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    protected $fillable = [
        'user_id', 'job_listing_id', 'cover_letter', 'resume_path', 'status', 'employer_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'shortlisted' => 'indigo',
            'rejected' => 'red',
            'hired' => 'green',
            default => 'gray',
        };
    }
}
