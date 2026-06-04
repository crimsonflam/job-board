<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobListing extends Model
{
    // MOD 5/15/17: removed expires_at (deadline), salary_currency, views_count,
    // and is_remote (remote is now a `type` value, not a flag).
    // MOD 1: removed `is_featured` — no featured-jobs feature anymore.
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'requirements', 'benefits', 'type', 'experience_level', 'education_level',
        'location', 'salary_min', 'salary_max', 'skills', 'status',
        'published_at',
    ];

    /**
     * MOD 3: Education levels localized to the Moroccan system. Display order
     * matters (used to render the filter/dropdown), so this array IS the order.
     */
    public const EDUCATION_LABELS = [
        'none' => 'No Requirements',
        'bac' => 'Bac',
        'bac+2' => 'Bac+2',
        'bac+3' => 'Bac+3',
        'bac+5' => 'Bac+5',
    ];

    /** MOD 2/14: The four allowed job types, value => label. */
    public const TYPE_LABELS = [
        'full-time' => 'Full-time',
        'part-time' => 'Part-time',
        'remote' => 'Remote',
        'internship' => 'Internship',
    ];

    /** MOD 13: Work-experience levels, value => label (also the display order). */
    public const EXPERIENCE_LABELS = [
        'entry_level' => 'Entry Level',
        'mid_level' => 'Mid Level',
        'senior' => 'Senior',
        'lead' => 'Lead',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'skills' => 'array',
            'published_at' => 'datetime',
        ];
    }

    /** MOD 2/14: A job is remote when its type is "remote" (no location). */
    public function isRemote(): bool
    {
        return $this->type === 'remote';
    }

    /**
     * The employer who posted this job. Company details (name, logo,
     * description, location, website, industry) are read from this user —
     * there is no separate Company model anymore.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    // MOD 5/16: "Published/active" now just means status='active'. There is no
    // deadline (expires_at) to check anymore — active jobs stay visible until
    // the employer deactivates or deletes them.
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // MOD 1: scopeFeatured() removed — featured jobs feature deleted.

    /**
     * WHAT: Applies the Browse Jobs search + filters to the query.
     * WHY:  Filtering in the DB (rather than loading all jobs and filtering in
     *       PHP) keeps the page fast as the listing count grows, and lets
     *       pagination work correctly on the filtered set.
     * HOW:  Each `when()` only adds its clause when that filter was provided,
     *       so an empty filter set returns all published jobs untouched.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            // MOD 2: Search by keyword across job title OR employer company name
            // OR location (case-insensitive partial match).
            ->when($filters['search'] ?? null, function ($q, $s) {
                $q->where(function ($inner) use ($s) {
                    $inner->where('title', 'like', "%{$s}%")
                        ->orWhere('location', 'like', "%{$s}%")
                        ->orWhereHas('user', fn ($u) => $u->where('company_name', 'like', "%{$s}%"));
                });
            })
            ->when($filters['category'] ?? null, fn ($q, $c) => $q->where('category_id', $c))
            // MOD 2: Job type multi-select (OR logic). Includes "remote" as a type.
            ->when($filters['type'] ?? null, fn ($q, $types) => $q->whereIn('type', (array) $types))
            // MOD 2: Location filter (a Moroccan city). Remote jobs have no
            // location so they naturally fall outside a location filter.
            ->when($filters['location'] ?? null, fn ($q, $l) => $q->where('location', $l))
            // MOD 13: Work-experience filter — multi-select, OR logic.
            ->when($filters['experience'] ?? null, fn ($q, $levels) => $q->whereIn('experience_level', (array) $levels))
            ->when($filters['education'] ?? null, fn ($q, $e) => $q->where('education_level', $e))
            // Salary range overlap.
            ->when($filters['salary_min'] ?? null, fn ($q, $s) => $q->where('salary_max', '>=', $s))
            ->when($filters['salary_max'] ?? null, fn ($q, $s) => $q->where('salary_min', '<=', $s));
        // MOD 4: time-based "posted date" filtering removed entirely.
    }

    /** MOD 3: Human-readable education requirement, e.g. "Bac+3". */
    public function educationLabel(): string
    {
        return self::EDUCATION_LABELS[$this->education_level] ?? 'No Requirements';
    }

    /** MOD 2: Human-readable job type, e.g. "Full-time". */
    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst($this->type);
    }

    /** MOD 13: Human-readable experience level, e.g. "Entry Level". */
    public function experienceLabel(): string
    {
        return self::EXPERIENCE_LABELS[$this->experience_level] ?? ucfirst($this->experience_level);
    }

    /**
     * MOD 15: Salary is always in MAD (no currency column). Returns e.g.
     * "8,000 - 15,000 MAD", or "Not specified" when no range is set.
     */
    public function salaryRange(): string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Not specified';
        }

        if ($this->salary_min && $this->salary_max) {
            return number_format($this->salary_min) . ' - ' . number_format($this->salary_max) . ' MAD';
        }

        return number_format($this->salary_min ?: $this->salary_max) . ' MAD';
    }
}
