<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ============================================================
 * WHAT: Route middleware that restricts a route group to specific roles.
 * WHY:  This is the backbone of role-based access control (RBAC). Routes are
 *       grouped by audience (seeker / employer / admin) and each group is
 *       guarded so a user can never reach another role's pages by typing the
 *       URL. Centralizing the check here means individual controllers don't
 *       have to repeat "is this the right role?" logic.
 * HOW:  Registered as the `role` alias (see bootstrap/app.php). Routes pass
 *       the allowed roles as parameters, e.g.
 *           Route::middleware('role:admin,super_admin')
 *       Laravel hands those to `$roles`. We allow the request through only if
 *       the logged-in user's role is in that list; otherwise we abort with 403.
 * ============================================================
 */
class RoleMiddleware
{
    /**
     * @param  Request   $request  The incoming HTTP request.
     * @param  Closure   $next     The next step in the middleware pipeline.
     * @param  string ...$roles    Allowed role names declared on the route
     *                             (e.g. 'admin', 'super_admin'). Variadic so a
     *                             route can permit one OR several roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Deny if (a) nobody is logged in, or (b) the user's role is not one of
        // the roles this route allows. `auth()->user()->role` is the single
        // 'seeker' | 'employer' | 'admin' | 'super_admin' value on the user row.
        // Note: super_admin is NOT auto-included in 'admin' here — routes that
        // want both must list both (e.g. 'role:admin,super_admin'), which keeps
        // the rule explicit and easy to audit.
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            // 403 Forbidden: authenticated-but-wrong-role, or unauthenticated.
            abort(403, 'Unauthorized.');
        }

        // Authorized — pass the request along to the next middleware/controller.
        return $next($request);
    }
}
