<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedJob extends Model
{
    protected $fillable = ['user_id', 'job_listing_id'];

    // The seeker who saved the job.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    // The job that was saved.
    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }
}
