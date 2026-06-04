<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * ============================================================
 * WHAT: Admin user management — list users and deactivate/activate accounts.
 *       Crucially, this controller NEVER deletes users (MOD 4): accounts are
 *       preserved and merely locked out, so access can be restored anytime.
 * WHY:  Deactivation is reversible and loses no data, unlike deletion.
 * PERMISSIONS (who can do what — MOD 5, enforced via User::canManage()):
 *   - Super Admin: can deactivate/activate ANYONE except another super admin
 *                  (and never themselves).
 *   - Normal Admin: can deactivate/activate REGULAR USERS only (seeker /
 *                   employer) — never other admins or the super admin.
 *   - Nobody can act on their own account here.
 *   Every state-changing method re-checks this on the SERVER (abort 403),
 *   independent of whether the UI showed the button — the server is the
 *   source of truth, so a hand-crafted/forged request can't bypass it.
 * ============================================================
 */
class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->when($request->search, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * ============================================================
     * MOD 4: Deactivate a user (instead of deleting).
     * WHY: Preserves the account + data; the user is simply locked out and
     *      can be reactivated later. Far safer than deletion (no data loss).
     * MOD 5: Authorization via the role hierarchy. The acting admin must be
     *      allowed to manage the target (User::canManage):
     *        - super_admin → anyone except another super_admin
     *        - admin       → regular users (seeker/employer) only
     *        - never yourself
     *      Enforced here on the SERVER (the UI also hides the buttons, but
     *      the server is the source of truth — a forged request gets 403).
     * ============================================================
     */
    public function deactivate(User $user)
    {
        abort_unless(auth()->user()->canManage($user), 403);

        $user->update(['status' => 'deactivated']);

        // Note: their existing session isn't force-killed here, but they can
        // no longer log in again, and any new login attempt is rejected.
        return back()->with('success', 'User deactivated successfully.');
    }

    /**
     * MOD 4: Reactivate a deactivated user (same permission rules as above).
     */
    public function activate(User $user)
    {
        abort_unless(auth()->user()->canManage($user), 403);

        $user->update(['status' => 'active']);

        return back()->with('success', 'User activated successfully.');
    }

    // MOD 4: destroy() removed — admins deactivate/activate instead of deleting,
    // so no user data is ever lost.
}
