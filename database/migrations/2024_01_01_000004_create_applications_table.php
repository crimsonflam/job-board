<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();

            // ----------------------------------------------------------------
            // CV used for THIS application.
            // WHY: A seeker can apply with their default CV OR upload a new one
            //      just for this job. We store the CV path + display name on the
            //      APPLICATION (not the user) so that if the seeker later changes
            //      their default CV, this application's CV stays exactly as sent.
            // ----------------------------------------------------------------
            $table->string('resume_path')->nullable();      // storage path of the CV file
            $table->string('resume_file_name')->nullable();  // original filename shown in the UI
            $table->boolean('cv_is_default')->default(true); // true = used default CV, false = uploaded for this job

            // ----------------------------------------------------------------
            // Application status + employer response.
            // WHY: The seeker-facing model is intentionally simple — an
            //      application is either awaiting a reply, accepted, or rejected.
            //      `pending` means "No Response Yet". When the employer replies
            //      they set the status, write a `response_message`, and we stamp
            //      `responded_at`.
            // ----------------------------------------------------------------
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('response_message')->nullable();    // employer's message to the applicant
            $table->timestamp('responded_at')->nullable();   // when the employer replied

            $table->timestamps();

            // A seeker can only apply once per job.
            $table->unique(['user_id', 'job_listing_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
