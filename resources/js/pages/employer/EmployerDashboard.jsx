import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { employerApi } from '../../api';
import { useAuth } from '../../contexts/AuthContext';
import { FullPageLoader } from '../../components/guards';
import { formatDate } from '../../utils';

/* Port of resources/views/employer/dashboard.blade.php. */
export default function EmployerDashboard() {
    const { user } = useAuth();
    const [data, setData] = useState(null);

    useEffect(() => {
        employerApi.dashboard().then(({ data }) => setData(data));
    }, []);

    if (!data) return <FullPageLoader />;

    const statusMeta = (status) => {
        if (status === 'accepted') return ['bg-green-100 text-green-800', 'Accepted'];
        if (status === 'rejected') return ['bg-red-100 text-red-800', 'Rejected'];
        return ['bg-gray-100 text-gray-700', 'Awaiting Reply'];
    };

    const jobStats = data.job_stats ?? {};

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div className="flex items-center space-x-3">
                    <h1 className="text-2xl font-bold text-gray-900">{user?.company_name ?? 'My Company'}</h1>
                </div>
                <div className="mt-4 sm:mt-0 flex space-x-3">
                    <Link to="/employer/jobs/create" className="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" /></svg>
                        Post New Job
                    </Link>
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div className="text-sm font-medium text-gray-500">Active Jobs</div>
                    <div className="mt-2 text-3xl font-bold text-gray-900">{data.active_jobs}</div>
                </div>
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div className="text-sm font-medium text-gray-500">Total Applications</div>
                    <div className="mt-2 text-3xl font-bold text-gray-900">{data.total_applications}</div>
                </div>
                {Object.entries(jobStats).map(([statusName, count]) => (
                    <div key={statusName} className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="text-sm font-medium text-gray-500">{statusName.charAt(0).toUpperCase() + statusName.slice(1)} Jobs</div>
                        <div className="mt-2 text-3xl font-bold text-gray-900">{count}</div>
                    </div>
                ))}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <Link to="/employer/jobs/create" className="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-4 hover:border-primary-300 hover:shadow-md transition">
                    <div className="flex-shrink-0 w-10 h-10 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div>
                        <div className="text-sm font-semibold text-gray-900">Post New Job</div>
                        <div className="text-xs text-gray-500">Create a new listing</div>
                    </div>
                </Link>
                <Link to="/employer/applicants" className="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-4 hover:border-primary-300 hover:shadow-md transition">
                    <div className="flex-shrink-0 w-10 h-10 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div>
                        <div className="text-sm font-semibold text-gray-900">View Applicants</div>
                        <div className="text-xs text-gray-500">Review candidates</div>
                    </div>
                </Link>
                <Link to="/employer/company" className="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-4 hover:border-primary-300 hover:shadow-md transition">
                    <div className="flex-shrink-0 w-10 h-10 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-5m-9 0H3m2 0h5" /></svg>
                    </div>
                    <div>
                        <div className="text-sm font-semibold text-gray-900">Company Profile</div>
                        <div className="text-xs text-gray-500">Update your company info</div>
                    </div>
                </Link>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200">
                <div className="px-6 py-4 border-b border-gray-200">
                    <h2 className="text-lg font-semibold text-gray-900">Recent Applications</h2>
                </div>
                {data.recent_applications.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {data.recent_applications.map((app) => {
                                    const [cls, label] = statusMeta(app.status);
                                    return (
                                        <tr key={app.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{app.user?.name}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{app.job_listing?.title}</td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${cls}`}>{label}</span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{formatDate(app.created_at)}</td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="px-6 py-12 text-center text-gray-500 text-sm">No applications received yet.</div>
                )}
            </div>
        </div>
    );
}
