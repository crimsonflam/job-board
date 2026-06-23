import { useEffect } from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { useFlash } from '../contexts/FlashContext';

/* A full-screen centered spinner shown while auth state is resolving. */
export function FullPageLoader() {
    return (
        <div className="min-h-[60vh] flex items-center justify-center">
            <svg className="w-8 h-8 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
        </div>
    );
}

/* Requires an authenticated user; otherwise bounces to /login (remembering the
   intended path so login can return there, mirroring redirect()->intended()). */
export function RequireAuth({ children }) {
    const { user, loading } = useAuth();
    const location = useLocation();

    if (loading) return <FullPageLoader />;
    if (!user) return <Navigate to="/login" state={{ from: location }} replace />;
    return children;
}

/*
 * Requires the user to hold one of `roles` (the server still enforces this on
 * every API call — this is convenience routing only). For employers, an optional
 * `requireCompanyProfile` mirrors the old server gate that pushed an employer
 * with no company profile to set one up first.
 */
export function RequireRole({ roles, requireCompanyProfile = false, children }) {
    const { user, loading } = useAuth();
    const { flash } = useFlash();
    const location = useLocation();

    const needsProfile =
        requireCompanyProfile && user?.is_employer && !user?.has_company_profile;

    useEffect(() => {
        if (needsProfile) {
            flash('info', 'Please set up your company profile first.');
        }
    }, [needsProfile, flash]);

    if (loading) return <FullPageLoader />;
    if (!user) return <Navigate to="/login" state={{ from: location }} replace />;
    if (!roles.includes(user.role)) return <Navigate to="/dashboard" replace />;
    if (needsProfile) return <Navigate to="/employer/company" replace />;

    return children;
}

/* Guest-only routes (login/register): authenticated users go to their dashboard. */
export function RequireGuest({ children }) {
    const { user, loading } = useAuth();
    if (loading) return <FullPageLoader />;
    if (user) return <Navigate to="/dashboard" replace />;
    return children;
}
