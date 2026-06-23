import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { adminApi } from '../../api';
import { FullPageLoader } from '../../components/guards';
import { formatDate } from '../../utils';

/* Port of resources/views/admin/dashboard.blade.php. */
export default function AdminDashboard() {
    const [data, setData] = useState(null);

    useEffect(() => {
        adminApi.dashboard().then(({ data }) => setData(data));
    }, []);

    if (!data) return <FullPageLoader />;

    const s = data.stats;
    const num = (n) => Number(n ?? 0).toLocaleString();

    const jobStatus = (status) => {
        if (status === 'active') return ['bg-green-100 text-green-800', 'Active'];
        if (status === 'pending') return ['bg-yellow-100 text-yellow-800', 'Pending'];
        return ['bg-gray-100 text-gray-800', status.charAt(0).toUpperCase() + status.slice(1)];
    };

    const roleBadge = (role) => {
        if (role === 'admin' || role === 'super_admin') return ['bg-red-100 text-red-800', role === 'super_admin' ? 'Super Admin' : 'Admin'];
        if (role === 'employer') return ['bg-blue-100 text-blue-800', 'Employer'];
        return ['bg-purple-100 text-purple-800', 'Seeker'];
    };

    const statCard = (label, value, valueClass = 'text-gray-900') => (
        <div className="bg-white rounded-lg border border-gray-200 p-6">
            <div className="text-sm font-medium text-gray-500">{label}</div>
            <div className={`mt-2 text-3xl font-bold ${valueClass}`}>{num(value)}</div>
        </div>
    );

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 className="text-2xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                {statCard('Total Users', s.total_users)}
                {statCard('Seekers', s.seekers, 'text-indigo-600')}
                {statCard('Employers', s.employers, 'text-emerald-600')}
                {statCard('Total Jobs', s.total_jobs)}
                {statCard('Active Jobs', s.active_jobs, 'text-green-600')}
                {statCard('Total Applications', s.total_applications, 'text-primary-600')}
                {statCard('Active Users', s.active_users, 'text-green-600')}
                {statCard('Deactivated Users', s.deactivated_users, 'text-gray-500')}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {/* Recent Job Listings */}
                <div className="bg-white rounded-lg border border-gray-200">
                    <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 className="text-lg font-semibold text-gray-900">Recent Job Listings</h2>
                        <Link to="/admin/jobs" className="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</Link>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left font-medium text-gray-500">Title</th>
                                    <th className="px-6 py-3 text-left font-medium text-gray-500">Company</th>
                                    <th className="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                                    <th className="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100">
                                {data.recent_jobs.length > 0 ? data.recent_jobs.map((job) => {
                                    const [cls, label] = jobStatus(job.status);
                                    return (
                                        <tr key={job.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-3 font-medium text-gray-900">{job.title?.slice(0, 30)}</td>
                                            <td className="px-6 py-3 text-gray-600">{job.company?.company_name ?? 'N/A'}</td>
                                            <td className="px-6 py-3"><span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}`}>{label}</span></td>
                                            <td className="px-6 py-3 text-gray-500">{formatDate(job.created_at)}</td>
                                        </tr>
                                    );
                                }) : (
                                    <tr><td colSpan={4} className="px-6 py-8 text-center text-gray-400">No job listings yet.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Recent Users */}
                <div className="bg-white rounded-lg border border-gray-200">
                    <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 className="text-lg font-semibold text-gray-900">Recent Users</h2>
                        <Link to="/admin/users" className="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</Link>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                                    <th className="px-6 py-3 text-left font-medium text-gray-500">Email</th>
                                    <th className="px-6 py-3 text-left font-medium text-gray-500">Role</th>
                                    <th className="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100">
                                {data.recent_users.length > 0 ? data.recent_users.map((u) => {
                                    const [cls, label] = roleBadge(u.role);
                                    return (
                                        <tr key={u.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-3 font-medium text-gray-900">{u.name}</td>
                                            <td className="px-6 py-3 text-gray-600">{u.email}</td>
                                            <td className="px-6 py-3"><span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}`}>{label}</span></td>
                                            <td className="px-6 py-3 text-gray-500">{formatDate(u.created_at)}</td>
                                        </tr>
                                    );
                                }) : (
                                    <tr><td colSpan={4} className="px-6 py-8 text-center text-gray-400">No users yet.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
}
