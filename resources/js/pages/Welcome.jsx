import { Link } from 'react-router-dom';

/*
 * Port of resources/views/home.blade.php — the guest landing page (two-button
 * hero). Authenticated users never see this (the router sends them to their
 * dashboard). Both CTAs route to /login, exactly like the original.
 */
export default function Welcome() {
    return (
        <section className="flex-1 flex items-center justify-center px-4 py-20 sm:py-28">
            <div className="w-full max-w-3xl mx-auto text-center">
                <div className="flex items-center justify-center space-x-3 mb-6">
                    <svg className="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span className="text-4xl font-bold tracking-tight text-gray-900">Job<span className="text-primary-600">Board</span></span>
                </div>

                <h1 className="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">
                    Connect Talent with Employers
                </h1>
                <p className="mt-4 text-base sm:text-lg font-light text-gray-600 max-w-xl mx-auto">
                    Find your next opportunity, or your next great hire. Choose how you'd like to get started.
                </p>

                <div className="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                    <Link to="/login" className="w-full sm:w-auto inline-flex items-center justify-center min-w-[220px] min-h-[64px] px-8 py-4 text-base font-semibold text-white bg-primary-600 rounded-xl shadow-sm hover:bg-primary-700 transition-colors duration-300">
                        <svg className="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        Looking for a Job
                    </Link>

                    <Link to="/login" className="w-full sm:w-auto inline-flex items-center justify-center min-w-[220px] min-h-[64px] px-8 py-4 text-base font-semibold text-white bg-primary-600 rounded-xl shadow-sm hover:bg-primary-700 transition-colors duration-300">
                        <svg className="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01" /></svg>
                        Looking for Employees
                    </Link>
                </div>

                <p className="mt-8 text-sm font-light text-gray-500">
                    New here?{' '}
                    <Link to="/register" className="font-medium text-primary-600 hover:text-primary-700 transition-colors">
                        Create an account
                    </Link>
                </p>
            </div>
        </section>
    );
}
