<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ============================================================
 * WHAT: A "bookmark" linking one seeker to one job they saved (MOD 7 of the
 *       earlier round — the heart toggle on job cards).
 * WHY:  A simple join table (user ↔ job_listing) is the cleanest way to model
 *       a many-to-many "saved" relationship. A DB unique index on
 *       (user_id, job_listing_id) prevents saving the same job twice.
 * HOW:  Created/deleted by SavedJobController@toggle; the Saved Jobs page reads
 *       jobs whereHas('savedBy') for the current user.
 * ============================================================
 */
class SavedJob extends Model
{
    // Only these two foreign keys are mass-assignable — nothing else to set.
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
