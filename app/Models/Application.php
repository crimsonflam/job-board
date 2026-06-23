<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'user_id', 'job_listing_id',
        'resume_path', 'resume_file_name', 'cv_is_default',
        'status', 'response_message', 'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'cv_is_default' => 'boolean',
            'responded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }
    public function hasResponse(): bool
    {
        return $this->status !== 'pending';
    }

    
    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'accepted' => 'green',
            'rejected' => 'red',
            default => 'gray', 
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            default => 'No Response Yet',
        };
    }
}
