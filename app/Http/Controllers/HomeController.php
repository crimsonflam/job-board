<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    // ============================================================
    // WHAT: Renders the guest landing page (two-button hero).
    // WHY:  The redesign requires that logged-in users NEVER see the
    //       marketing home page again — once authenticated they belong
    //       on their role-specific dashboard. So before rendering the
    //       guest view we short-circuit authenticated users to the
    //       generic /dashboard route, which itself fans out to the
    //       admin / employer / seeker dashboard based on role
    //       (see DashboardController).
    //
    //       The previous version eagerly loaded featured jobs, latest
    //       jobs, categories and "top companies" for a rich landing
    //       page. The new minimal hero needs none of that data, so the
    //       queries were removed — fewer DB round-trips on every visit.
    // ============================================================
    public function index()
    {
        // Authenticated users are routed to their dashboard, not the
        // guest home page. `route('dashboard')` resolves the correct
        // role dashboard via DashboardController.
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Guests see the minimal two-button landing page.
        return view('home');
    }
}
