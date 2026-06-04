<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ============================================================
 * WHAT: A job category/industry (e.g. Technology, Design, Finance).
 * WHY:  Categories let jobs be grouped and filtered on the Browse Jobs page.
 *       They're seeded once and rarely change, so they live in their own
 *       lightweight table rather than as a free-text field on each job.
 * HOW:  Each job belongs to a category via job_listings.category_id; this
 *       model exposes the inverse — all jobs in a category.
 * ============================================================
 */
class Category extends Model
{
    // `slug` is the URL-friendly form; `icon` is a short key used in the UI.
    protected $fillable = ['name', 'slug', 'icon'];

    // All job listings filed under this category (one category → many jobs).
    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }
}
