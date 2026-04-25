<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobListing extends Model
{
    protected $fillable = [
        'company_id', 'user_id', 'category_id', 'title', 'slug', 'description',
        'requirements', 'benefits', 'type', 'experience_level', 'location', 'is_remote',
        'salary_min', 'salary_max', 'salary_currency', 'skills', 'status', 'is_featured',
        'published_at', 'expires_at', 'views_count',
    ];

    protected function casts(): array
    {
        return [
            'is_remote' => 'boolean',
            'is_featured' => 'boolean',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'skills' => 'array',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

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

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['category'] ?? null, fn ($q, $c) => $q->where('category_id', $c))
            ->when($filters['type'] ?? null, fn ($q, $t) => $q->where('type', $t))
            ->when($filters['location'] ?? null, fn ($q, $l) => $q->where('location', 'like', "%{$l}%"))
            ->when(isset($filters['remote']) && $filters['remote'], fn ($q) => $q->where('is_remote', true))
            ->when($filters['experience'] ?? null, fn ($q, $e) => $q->where('experience_level', $e))
            ->when($filters['salary_min'] ?? null, fn ($q, $s) => $q->where('salary_min', '>=', $s))
            ->when($filters['salary_max'] ?? null, fn ($q, $s) => $q->where('salary_max', '<=', $s));
    }

    public function salaryRange(): string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Not specified';
        }

        $currency = $this->salary_currency;
        if ($this->salary_min && $this->salary_max) {
            return "$currency " . number_format($this->salary_min) . ' - ' . number_format($this->salary_max);
        }

        return "$currency " . number_format($this->salary_min ?: $this->salary_max);
    }
}
