<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\Request;

/**
 * ============================================================
 * WHAT: Admin moderation of job listings — view all jobs and DELETE them.
 * WHY:  Admins moderate content but do NOT own jobs, so (MOD 2) they cannot
 *       change a job's status — only the employer who created it can. The
 *       admin's single power here is removing a job entirely. Status is shown
 *       read-only for context.
 * HOW:  `index()` lists/searches all jobs (no status filter — MOD 3);
 *       `destroy()` permanently deletes one.
 * ============================================================
 */
class AdminJobController extends Controller
{
    /**
     * MOD 3: No status filter. The admin sees ALL jobs regardless of status;
     * only search (title/company) remains. Status changes belong to the
     * employer who owns the job — the admin's only job action is delete.
     */
    public function index(Request $request)
    {
        $jobs = JobListing::with(['user', 'category'])
            ->when($request->search, function ($q, $s) {
                // Search by job title OR employer company name.
                $q->where('title', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($u) => $u->where('company_name', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(20);

        return view('admin.jobs.index', compact('jobs'));
    }

    // MOD 2: updateStatus() removed — admins cannot change job status, only
    // the employer can. This prevents accidental status changes by admins.

    /**
     * MOD 2: The admin's only job action — permanently delete a job.
     */
    public function destroy(JobListing $jobListing)
    {
        $jobListing->delete();
        return back()->with('success', 'Job deleted successfully.');
    }
}
