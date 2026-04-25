<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'avatar', 'bio',
        'location', 'website', 'resume_path', 'skills', 'expected_salary', 'availability',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'skills' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isSeeker(): bool
    {
        return $this->role === 'seeker';
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function jobAlerts(): HasMany
    {
        return $this->hasMany(JobAlert::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seeker_id')
            ->orWhere('employer_id', $this->id);
    }

    public function hasSavedJob(int $jobListingId): bool
    {
        return $this->savedJobs()->where('job_listing_id', $jobListingId)->exists();
    }

    public function hasApplied(int $jobListingId): bool
    {
        return $this->applications()->where('job_listing_id', $jobListingId)->exists();
    }
}
