<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * ============================================================
 * WHAT: Handles new-account sign-up (the registration page + form submit).
 * WHY:  Self-service registration is limited to the two PUBLIC roles —
 *       seeker and employer. Privileged roles (admin / super_admin) can NEVER
 *       be created here; they are seeded/managed separately, which prevents
 *       anyone from registering themselves an admin account.
 * HOW:  `showForm()` renders the page; `register()` validates input, creates
 *       the user (password hashed), logs them in, and routes them to their
 *       role dashboard.
 * ============================================================
 */
class RegisterController extends Controller
{
    /**
     * WHAT: Show the registration form. WHY/HOW: trivial — just renders the view.
     */
    public function showForm()
    {
        return view('auth.register');
    }

    /**
     * WHAT: Validate the submitted data and create the account.
     * HOW:  Validate → create user → log in → redirect to the role dashboard.
     */
    public function register(Request $request)
    {
        // Validate every field server-side (never trust client input):
        //  - email must be a well-formed, UNIQUE address (no duplicate accounts).
        //  - password: min 8 chars AND `confirmed` (must match password_confirmation).
        //  - role is constrained to seeker|employer ONLY. This is the security
        //    gate that stops a user from registering as an admin/super_admin.
        //  - company_name is required ONLY when role=employer (required_if).
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:seeker,employer'],
            'company_name' => ['required_if:role,employer', 'nullable', 'string', 'max:255'],
        ]);

        // Create the user. Notes:
        //  - Hash::make() one-way-hashes the password; the plaintext is never stored.
        //  - Company info lives directly on the user row (there is no separate
        //    Company table). Only an employer gets a company_name; for a seeker
        //    it stays null. `status` defaults to 'active' at the DB level.
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'company_name' => $validated['role'] === 'employer'
                ? ($validated['company_name'] ?? null)
                : null,
        ]);

        // Log the brand-new user straight in (no separate login step needed).
        Auth::login($user);

        // Send them to the generic dashboard route, which fans out by role
        // (see DashboardController) to the seeker/employer dashboard.
        return redirect()->route('dashboard');
    }
}
