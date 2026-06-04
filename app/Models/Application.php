<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    // Cover letters are not part of this application's workflow — an
    // application is a CV/resume, its status, and the employer's response.
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

    // MOD 19: conversation() relation removed — messaging feature deleted.
    // The employer's accept/reject `response_message` is the only communication.

    /**
     * Whether the employer has responded yet.
     * `pending` is the "No Response Yet" state.
     */
    public function hasResponse(): bool
    {
        return $this->status !== 'pending';
    }

    /**
     * Tailwind colour key for the status badge.
     * pending = gray ("No Response"), accepted = green, rejected = red.
     */
    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'accepted' => 'green',
            'rejected' => 'red',
            default => 'gray', // pending / no response yet
        };
    }

    /** Human-readable status label for the seeker view. */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            default => 'No Response Yet',
        };
    }
}
