<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'status', 'phone', 'bio',
        'location', 'website', 'resume_path', 'skills', 'expected_salary', 'availability',
        'company_name', 'company_description',
        'company_location', 'company_website', 'industry',
        'resume_file_name', 'resume_uploaded_at',
    ];
// hadchi makitsiftch l ffrontend
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
            'resume_uploaded_at' => 'datetime',
        ];
    }

    //reusable fucntion for roless(called in conlrollers)
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isSeeker(): bool
    {
        return $this->role === 'seeker';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canManage(User $target): bool
    {
        //admin cant manage self or sup adm
        if ($this->id === $target->id) {
            return false; 
        }

        if ($this->isSuperAdmin()) {
            return !$target->isSuperAdmin();
        }

        if ($this->role === 'admin') {
            // Regular users only (not admins, not super admin).
            return in_array($target->role, ['seeker', 'employer']);
        }

        return false;
    }
//relation user 1 to many job listings
    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }
//check if company name exist
    public function hasCompanyProfile(): bool
    {
        return filled($this->company_name);
    }
//if no company name then return user name
    public function companyDisplayName(): string
    {
        return $this->company_name ?: $this->name;
    }
//relation user 1 applications many
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
//user 1 to many saved jobs
    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

// this function checks if the user has saved a specific job listing
    public function hasSavedJob(int $jobListingId): bool
    {
        return $this->savedJobs()->where('job_listing_id', $jobListingId)->exists();
    }
//check if applied
    public function hasApplied(int $jobListingId): bool
    {
        return $this->applications()->where('job_listing_id', $jobListingId)->exists();
    }

   // check if the user has uploaded a resume
    public function hasDefaultResume(): bool
    {
        return filled($this->resume_path);
    }
}
