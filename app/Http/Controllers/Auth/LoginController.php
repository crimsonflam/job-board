<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    /**
     * ============================================================
     * MOD 4: Reject deactivated accounts at login.
     * WHAT: After the password is verified, we check the account status.
     *       A 'deactivated' user is immediately logged back out and shown a
     *       support message — they never reach a dashboard.
     * WHY:  Deactivation locks a user out without deleting their data, so
     *       admins can restore access later. The check happens AFTER
     *       credential verification so the deactivation message is only shown
     *       to someone who owns the account (not leaked on a wrong password).
     * ============================================================
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Credentials valid — now enforce the account-status gate.
            if (!Auth::user()->isActive()) {
                // Roll the session back: do NOT keep them logged in.
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
