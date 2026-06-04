<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // MOD 5: role hierarchy. 'super_admin' sits above 'admin'. A normal
            // admin can only manage regular users (seeker/employer); a super
            // admin can also manage normal admins. There is exactly one
            // super_admin and it cannot be created/demoted through the UI.
            $table->enum('role', ['seeker', 'employer', 'admin', 'super_admin'])->default('seeker');
            // MOD 4: account status. Deactivated accounts cannot log in but are
            // preserved in the DB (admins can reactivate them anytime).
            $table->enum('status', ['active', 'deactivated'])->default('active');
            $table->string('phone')->nullable();
            // MOD 9: Profile pictures/avatars removed — no image uploads for
            // seekers (CV is the only file). `bio` is capped at 250 chars in
            // validation; `website` is an optional portfolio link.
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            // Seeker's DEFAULT CV used to pre-fill job applications.
            // resume_file_name = original filename shown in the profile/apply UI;
            // resume_uploaded_at = when it was uploaded (shown as "Uploaded on …").
            $table->string('resume_path')->nullable();
            $table->string('resume_file_name')->nullable();
            $table->timestamp('resume_uploaded_at')->nullable();
            $table->json('skills')->nullable();
            $table->string('expected_salary')->nullable();
            $table->enum('availability', ['available', 'open', 'not_available'])->default('available');

            // ----------------------------------------------------------------
            // Employer "company" fields.
            // WHY: The standalone `companies` table was removed. A company is
            //      simply how an EMPLOYER user presents themselves, so these
            //      fields live directly on the user. A job reads its company
            //      info through its employer (job_listings.user_id → users).
            //      Only employers populate these; they stay null for seekers.
            // ----------------------------------------------------------------
            // MOD 18: Company branding/images removed — no company_logo/banner.
            // Only text fields remain for the employer's company profile.
            $table->string('company_name')->nullable();
            $table->text('company_description')->nullable();
            $table->string('company_location')->nullable();
            $table->string('company_website')->nullable();
            $table->string('industry')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
