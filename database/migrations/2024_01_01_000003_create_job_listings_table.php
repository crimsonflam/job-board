<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            // A job belongs to its employer (a user). Company details
            // (name, logo, etc.) are read from that employer's user record —
            // there is no separate companies table anymore.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            // MOD 2 & 14: Job types reduced to four. "Remote" is now a TYPE
            // (not a separate boolean) — when type='remote' the job has no
            // location. Contract & Temporary/Freelance were removed.
            $table->enum('type', ['full-time', 'part-time', 'remote', 'internship'])->default('full-time');
            // MOD 13: Required work-experience level (now a required field on
            // creation and a multi-select Browse filter).
            $table->enum('experience_level', ['entry_level', 'mid_level', 'senior', 'lead'])->default('entry_level');
            // MOD 3: Education levels localized to the Moroccan system
            // (Bac / Bac+2 / Bac+3 / Bac+5) plus "No Requirement".
            $table->enum('education_level', ['none', 'bac', 'bac+2', 'bac+3', 'bac+5'])->default('none');
            // MOD 2/14: Location is a Moroccan city, NULL for remote jobs.
            $table->string('location')->nullable();
            // MOD 15: Salary is always in MAD — no currency column.
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->json('skills')->nullable();
            // MOD 16: No "draft" state — jobs are active on creation. Employers
            // can deactivate (hide) or delete. Only active/inactive remain.
            $table->enum('status', ['active', 'inactive'])->default('active');
            // MOD 1: "featured" jobs feature removed — there is no premium
            // placement; all jobs display equally. No is_featured column.
            $table->timestamp('published_at')->nullable();
            // MOD 5: deadline (expires_at) removed — jobs stay active indefinitely.
            // MOD 17: views_count removed — only applicant counts matter now.
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
