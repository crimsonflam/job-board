import { useEffect, useRef, useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { useFlash } from '../contexts/FlashContext';

/*
 * Port of resources/views/layouts/app.blade.php — the app chrome: sticky navbar
 * with role-based links + user dropdown + mobile menu, the flash-message banners
 * (now driven by FlashContext instead of session flashes), and the footer.
 */

const BriefcaseLogo = ({ className }) => (
    <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
    </svg>
);

function FlashBanners() {
    const { messages, dismiss } = useFlash();

    const styles = {
        success: { box: 'bg-green-50 border-green-200', text: 'text-green-800', icon: 'text-green-500', path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        error: { box: 'bg-red-50 border-red-200', text: 'text-red-800', icon: 'text-red-500', path: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        info: { box: 'bg-gray-50 border-gray-200', text: 'text-gray-800', icon: 'text-gray-500', path: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };

    if (messages.length === 0) return null;

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 space-y-3">
            {messages.map((m) => {
                const s = styles[m.type] ?? styles.info;
                return (
                    <div key={m.id} className={`rounded-lg ${s.box} border p-4 flex items-center space-x-3`} role="button" onClick={() => dismiss(m.id)}>
                        <svg className={`w-5 h-5 ${s.icon} flex-shrink-0`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d={s.path} />
                        </svg>
                        <p className={`text-sm font-medium ${s.text}`}>{m.text}</p>
                    </div>
                );
            })}
        </div>
    );
}

export default function Layout({ children }) {
    const { user, logout } = useAuth();
    const { flash } = useFlash();
    const location = useLocation();
    const navigate = useNavigate();
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);
    const dropdownRef = useRef(null);

    const path = location.pathname;
    const isActive = (prefix) => path === prefix || path.startsWith(prefix + '/') || path.startsWith(prefix);

    // Close the dropdown on any outside click (Alpine @click.away equivalent).
    useEffect(() => {
        function onClick(e) {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setDropdownOpen(false);
            }
        }
        document.addEventListener('mousedown', onClick);
        return () => document.removeEventListener('mousedown', onClick);
    }, []);

    // Collapse menus whenever we navigate.
    useEffect(() => {
        setDropdownOpen(false);
        setMobileOpen(false);
    }, [path]);

    const handleLogout = async () => {
        await logout();
        flash('success', 'You have been logged out.');
        navigate('/');
    };

    const navLink = (to, label, active) => (
        <Link
            to={to}
            className={`text-sm font-medium ${active ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600'} transition-colors`}
        >
            {label}
        </Link>
    );

    const mobileLink = (to, label, active) => (
        <Link
            to={to}
            className={`px-3 py-2 rounded-md text-sm font-medium ${active ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50'}`}
        >
            {label}
        </Link>
    );

    return (
        <div className="min-h-screen flex flex-col bg-[#FAFAFA] text-[#2C2C2C] antialiased">
            {/* Navigation */}
            <nav className="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        <div className="flex items-center space-x-8">
                            <Link to={user ? '/dashboard' : '/'} className="flex items-center space-x-2">
                                <BriefcaseLogo className="w-8 h-8 text-primary-600" />
                                <span className="text-xl font-bold text-gray-900">Job<span className="text-primary-600">Board</span></span>
                            </Link>

                            <div className="hidden md:flex items-center space-x-6">
                                {user?.is_seeker && (
                                    <>
                                        {navLink('/jobs', 'Browse Jobs', isActive('/jobs'))}
                                        {navLink('/seeker/saved-jobs', 'Saved Jobs', isActive('/seeker/saved-jobs'))}
                                    </>
                                )}
                                {user?.is_employer && (
                                    <>
                                        {navLink('/employer/jobs', 'My Jobs', isActive('/employer/jobs'))}
                                        {navLink('/employer/applicants', 'Applicants', isActive('/employer/applicants'))}
                                    </>
                                )}
                                {user?.is_admin && navLink('/admin/dashboard', 'Dashboard', isActive('/admin'))}
                            </div>
                        </div>

                        {/* Right: Auth */}
                        <div className="flex items-center space-x-4">
                            {!user && (
                                <>
                                    <Link to="/login" className="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">Login</Link>
                                    <Link to="/register" className="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Register</Link>
                                </>
                            )}

                            {user && (
                                <div className="relative" ref={dropdownRef}>
                                    <button
                                        onClick={() => setDropdownOpen((o) => !o)}
                                        className="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-primary-600 focus:outline-none transition-colors"
                                    >
                                        <div className="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                            <span className="text-primary-600 font-semibold text-sm">{user.name?.charAt(0).toUpperCase()}</span>
                                        </div>
                                        <span className="hidden sm:inline">{user.name}</span>
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    {dropdownOpen && (
                                        <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                            <Link to="/dashboard" className="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">Dashboard</Link>

                                            {user.is_seeker && (
                                                <>
                                                    <Link to="/seeker/saved-jobs" className="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">Saved Jobs</Link>
                                                    <Link to="/seeker/profile" className="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">Edit Profile</Link>
                                                </>
                                            )}

                                            {user.is_employer && (
                                                <Link to="/employer/company" className="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">Edit Company</Link>
                                            )}

                                            <div className="border-t border-gray-100 my-1" />

                                            <button onClick={handleLogout} className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600">
                                                Logout
                                            </button>
                                        </div>
                                    )}
                                </div>
                            )}

                            {/* Mobile menu toggle */}
                            <button className="md:hidden p-2 text-gray-500 hover:text-gray-700" onClick={() => setMobileOpen((o) => !o)}>
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {/* Mobile Navigation */}
                    {mobileOpen && (
                        <div className="md:hidden pb-4">
                            <div className="flex flex-col space-y-2">
                                {user?.is_seeker && (
                                    <>
                                        {mobileLink('/jobs', 'Browse Jobs', isActive('/jobs'))}
                                        {mobileLink('/seeker/saved-jobs', 'Saved Jobs', isActive('/seeker/saved-jobs'))}
                                        {mobileLink('/seeker/applications', 'My Applications', isActive('/seeker/applications'))}
                                    </>
                                )}
                                {user?.is_employer && (
                                    <>
                                        {mobileLink('/employer/jobs', 'My Jobs', isActive('/employer/jobs'))}
                                        {mobileLink('/employer/applicants', 'Applicants', isActive('/employer/applicants'))}
                                    </>
                                )}
                                {user?.is_admin && mobileLink('/admin/dashboard', 'Dashboard', isActive('/admin'))}
                            </div>
                        </div>
                    )}
                </div>
            </nav>

            {/* Flash Messages */}
            <FlashBanners />

            {/* Main Content */}
            <main className="flex-1">{children}</main>

            {/* Footer */}
            <footer className="bg-white border-t border-gray-200 mt-auto">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div className="col-span-1 md:col-span-2">
                            <Link to={user ? '/dashboard' : '/'} className="flex items-center space-x-2 mb-3">
                                <BriefcaseLogo className="w-7 h-7 text-primary-600" />
                                <span className="text-lg font-bold text-gray-900">Job<span className="text-primary-600">Board</span></span>
                            </Link>
                            <p className="text-sm text-gray-500 max-w-sm">
                                Connecting talented professionals with outstanding opportunities. Find your next career move today.
                            </p>
                        </div>

                        <div>
                            <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">For Job Seekers</h3>
                            <ul className="space-y-2">
                                <li><Link to="/jobs" className="text-sm text-gray-500 hover:text-primary-600 transition-colors">Browse Jobs</Link></li>
                            </ul>
                        </div>

                        <div>
                            <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">For Employers</h3>
                            <ul className="space-y-2">
                                <li><Link to="/register" className="text-sm text-gray-500 hover:text-primary-600 transition-colors">Post a Job</Link></li>
                            </ul>
                        </div>
                    </div>

                    <div className="border-t border-gray-200 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between">
                        <p className="text-sm text-gray-400">&copy; {new Date().getFullYear()} JobBoard. All rights reserved.</p>
                        <div className="flex space-x-6 mt-4 sm:mt-0">
                            <a href="#" className="text-sm text-gray-400 hover:text-indigo-600 transition-colors">Privacy Policy</a>
                            <a href="#" className="text-sm text-gray-400 hover:text-indigo-600 transition-colors">Terms of Service</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
