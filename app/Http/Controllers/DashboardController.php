<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;

/**
 * ============================================================
 * WHAT: The generic "/dashboard" entry point. It doesn't render anything
 *       itself — it forwards the user to the dashboard that matches their role.
 * WHY:  Every authenticated link/redirect can point at one stable route name
 *       (`dashboard`) without knowing the user's role. This one place decides
 *       where that actually lands, so role-routing logic isn't scattered around
 *       the app (login, navbar, home-page redirect all rely on it).
 * HOW:  Read the logged-in user, then redirect by role in priority order.
 * ============================================================
 */
class DashboardController extends Controller
{
    public function index()
    {
        // The currently authenticated user (guaranteed non-null: this route is
        // behind the `auth` middleware, so guests never reach here).
        $user = auth()->user();

        // Admins and super-admins share the same admin dashboard. `isAdmin()`
        // returns true for BOTH tiers, so this branch must come first (a
        // super_admin is also "an admin" for the purpose of landing here).
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Employers manage their own jobs and applicants.
        if ($user->isEmployer()) {
            return redirect()->route('employer.dashboard');
        }

        // Default / fallback: a job seeker. Putting seeker last means any
        // future non-privileged role still gets a sensible landing page.
        return redirect()->route('seeker.dashboard');
    }
}
