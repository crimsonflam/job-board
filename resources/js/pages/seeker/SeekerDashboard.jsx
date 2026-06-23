import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { seekerApi } from '../../api';
import { useAuth } from '../../contexts/AuthContext';
import { FullPageLoader } from '../../components/guards';
import { formatDate } from '../../utils';

/* Port of resources/views/seeker/dashboard.blade.php. */
export default function SeekerDashboard() {
    const { user } = useAuth();
    const [data, setData] = useState(null);

    useEffect(() => {
        seekerApi.dashboard().then(({ data }) => setData(data));
    }, []);

    if (!data) return <FullPageLoader />;

    const statusMeta = (status) => {
        if (status === 'accepted') return ['bg-green-100 text-green-800', 'Accepted'];
        if (status === 'rejected') return ['bg-red-100 text-red-800', 'Rejected'];
        return ['bg-gray-100 text-gray-700', 'No Response'];
    };

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-gray-900">Welcome back, {user?.name}!</h1>
                <p className="mt-1 text-gray-600">Here's an overview of your job search activity.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div className="bg-white rounded-lg shadow p-6">
                    <div className="flex items-center">
                        <div className="flex-shrink-0 bg-blue-100 rounded-full p-3">
                            <svg className="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-500">Total Applications</p>
                            <p className="text-2xl font-semibold text-gray-900">{data.applications_count}</p>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow p-6">
                    <div className="flex items-center">
                        <div className="flex-shrink-0 bg-pink-100 rounded-full p-3">
                            <svg className="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-500">Saved Jobs</p>
                            <p className="text-2xl font-semibold text-gray-900">{data.saved_jobs_count}</p>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow p-6">
                    <div className="flex items-center">
                        <div className="flex-shrink-0 bg-gray-100 rounded-full p-3">
                            <svg className="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-500">Awaiting Reply</p>
                            <p className="text-2xl font-semibold text-gray-900">{data.pending_count}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div className="lg:col-span-2">
                    <div className="bg-white rounded-lg shadow">
                        <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 className="text-lg font-semibold text-gray-900">Recent Applications</h2>
                            <Link to="/seeker/applications" className="text-sm text-blue-600 hover:text-blue-800">View All</Link>
                        </div>
                        <div className="divide-y divide-gray-200">
                            {data.recent_applications.length > 0 ? (
                                data.recent_applications.map((app) => {
                                    const [cls, label] = statusMeta(app.status);
                                    return (
                                        <div key={app.id} className="px-6 py-4 flex items-center justify-between">
                                            <div>
                                                <Link to={`/seeker/applications/${app.id}`} className="text-sm font-medium text-gray-900 hover:text-blue-600">
                                                    {app.job_listing?.title}
                                                </Link>
                                                <p className="text-sm text-gray-500">{app.job_listing?.company?.company_name}</p>
                                            </div>
                                            <div className="flex items-center space-x-4">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${cls}`}>{label}</span>
                                                <span className="text-xs text-gray-400">{formatDate(app.created_at)}</span>
                                            </div>
                                        </div>
                                    );
                                })
                            ) : (
                                <div className="px-6 py-8 text-center text-gray-500">
                                    <p>You haven't applied to any jobs yet.</p>
                                    <Link to="/jobs" className="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm">Browse Jobs</Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div>
                    <div className="bg-white rounded-lg shadow">
                        <div className="px-6 py-4 border-b border-gray-200">
                            <h2 className="text-lg font-semibold text-gray-900">Quick Links</h2>
                        </div>
                        <div className="p-6 space-y-3">
                            <Link to="/jobs" className="flex items-center px-4 py-3 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                <svg className="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                Browse Jobs
                            </Link>
                            <Link to="/seeker/profile" className="flex items-center px-4 py-3 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
                                <svg className="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                Edit Profile
                            </Link>
                            <Link to="/seeker/saved-jobs" className="flex items-center px-4 py-3 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
                                <svg className="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                Saved Jobs
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
