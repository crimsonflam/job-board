<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * ============================================================
 * WHAT: The single account model for EVERY person in the system — seekers,
 *       employers, normal admins and the super admin all live in this table,
 *       distinguished by the `role` column.
 * WHY:  One unified users table keeps auth simple and lets shared concerns
 *       (login, status/deactivation, profile) work the same for everyone.
 *       Role- and employer-specific data are just nullable columns on the row
 *       (e.g. company_name for employers, resume_path for seekers) — there are
 *       no separate profile tables to join.
 * HOW:  Role helpers (isSeeker/isEmployer/isAdmin/isSuperAdmin) gate behaviour;
 *       `status` drives the active/deactivated login gate; canManage() encodes
 *       the admin permission hierarchy. Relationships link a user to the jobs
 *       they posted (employer), the applications + saved jobs they made (seeker).
 * ============================================================
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // MOD 9: removed `avatar` (no profile pictures for seekers).
    // MOD 18: removed `company_logo` (no company branding/images).
    protected $fillable = [
        // MOD 4: `status` (active/deactivated) is admin-managed.
        'name', 'email', 'password', 'role', 'status', 'phone', 'bio',
        'location', 'website', 'resume_path', 'skills', 'expected_salary', 'availability',
        // Employer "company" fields (text only — no logo/branding).
        'company_name', 'company_description',
        'company_location', 'company_website', 'industry',
        // Seeker default CV metadata.
        'resume_file_name', 'resume_uploaded_at',
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
            'resume_uploaded_at' => 'datetime',
        ];
    }

    // MOD 5: `isAdmin()` returns true for BOTH admin tiers so existing
    // role-gated routes/UI ("admin area") keep working for the super admin.
    // Use `isSuperAdmin()` for the elevated checks.
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

    // MOD 4: a deactivated account is locked out (checked at login).
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * ============================================================
     * MOD 5: Role-hierarchy permission check.
     * WHAT: Whether THIS user (an admin) may deactivate/activate $target.
     * WHY:  Prevents privilege escalation — a normal admin must not be able
     *       to touch other admins or the super admin, and nobody may act on
     *       the super admin or on themselves.
     * RULES:
     *   - You can never manage yourself.
     *   - super_admin can manage anyone EXCEPT another super_admin (there is
     *     only one, and it cannot be deactivated).
     *   - normal admin can manage ONLY regular users (seeker/employer).
     *   - everyone else: no.
     * This is enforced on the server (controller) AND mirrored in the UI.
     * ============================================================
     */
    public function canManage(User $target): bool
    {
        if ($this->id === $target->id) {
            return false; // never manage your own account
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

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }

    /**
     * Whether this employer has filled in their company profile.
     * Used to gate job posting (an employer needs a company name first),
     * replacing the old "does a Company row exist?" check.
     */
    public function hasCompanyProfile(): bool
    {
        return filled($this->company_name);
    }

    /**
     * Display name for this employer's company, falling back to the
     * person's own name if they haven't set a company name yet.
     */
    public function companyDisplayName(): string
    {
        return $this->company_name ?: $this->name;
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    // MOD 8: jobAlerts() relation removed (job-alert feature deleted).
    // MOD 19: conversations() relation removed (messaging feature deleted).

    public function hasSavedJob(int $jobListingId): bool
    {
        return $this->savedJobs()->where('job_listing_id', $jobListingId)->exists();
    }

    public function hasApplied(int $jobListingId): bool
    {
        return $this->applications()->where('job_listing_id', $jobListingId)->exists();
    }

    /**
     * Whether the seeker has a default CV on file.
     * Used to gate job applications: a seeker must upload a default CV
     * (in their profile) before they can apply to any job.
     */
    public function hasDefaultResume(): bool
    {
        return filled($this->resume_path);
    }
}
