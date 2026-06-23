import { useCallback, useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { adminApi } from '../../api';
import Pagination from '../../components/Pagination';
import { FullPageLoader } from '../../components/guards';
import { cleanParams, formatDate } from '../../utils';

/* Port of resources/views/admin/users/index.blade.php — deactivate/activate. */
export default function AdminUsersIndex() {
    const [searchParams, setSearchParams] = useSearchParams();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [toolbar, setToolbar] = useState({
        search: searchParams.get('search') || '',
        role: searchParams.get('role') || '',
    });

    const search = searchParams.get('search') || '';
    const role = searchParams.get('role') || '';
    const page = searchParams.get('page') || '1';

    const fetchUsers = useCallback(async () => {
        setLoading(true);
        try {
            const { data: res } = await adminApi.users(cleanParams({ search, role, page }));
            setData(res);
        } finally {
            setLoading(false);
        }
    }, [search, role, page]);

    useEffect(() => { fetchUsers(); }, [fetchUsers]);
    useEffect(() => { setToolbar({ search, role }); }, [search, role]);

    const submit = (e) => {
        e.preventDefault();
        setSearchParams(cleanParams(toolbar));
    };

    const setStatus = async (user, activate) => {
        const verb = activate ? 'Activate' : 'Deactivate';
        const msg = activate ? 'Activate this user? They will be able to log in.' : 'Deactivate this user? They will no longer be able to log in.';
        if (!window.confirm(msg)) return;
        const { data: res } = activate ? await adminApi.activateUser(user.id) : await adminApi.deactivateUser(user.id);
        const updated = res.data;
        setData((d) => ({ ...d, data: d.data.map((u) => (u.id === user.id ? { ...u, status: updated.status } : u)) }));
        void verb;
    };

    const roleBadge = (r) => {
        if (r === 'super_admin') return ['bg-amber-100 text-amber-800', 'Super Admin'];
        if (r === 'admin') return ['bg-red-100 text-red-800', 'Admin'];
        if (r === 'employer') return ['bg-gray-100 text-gray-700', 'Employer'];
        return ['bg-gray-100 text-gray-700', 'Seeker'];
    };

    const users = data?.data ?? [];

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="flex items-center justify-between mb-8">
                <h1 className="text-2xl font-bold text-gray-900">Manage Users</h1>
                <Link to="/admin/dashboard" className="text-sm text-primary-600 hover:text-primary-800 font-medium">Back to Dashboard</Link>
            </div>

            <div className="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                <form onSubmit={submit} className="flex flex-col sm:flex-row gap-4">
                    <div className="flex-1">
                        <input type="text" value={toolbar.search} onChange={(e) => setToolbar((t) => ({ ...t, search: e.target.value }))} placeholder="Search users..." className="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-2 border" />
                    </div>
                    <div className="sm:w-48">
                        <select value={toolbar.role} onChange={(e) => setToolbar((t) => ({ ...t, role: e.target.value }))} className="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-2 border">
                            <option value="">All Roles</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="employer">Employer</option>
                            <option value="seeker">Seeker</option>
                        </select>
                    </div>
                    <button type="submit" className="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Filter</button>
                </form>
            </div>

            <div className="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Email</th>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Role</th>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Joined</th>
                                <th className="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {loading ? (
                                <tr><td colSpan={6} className="px-6 py-12"><FullPageLoader /></td></tr>
                            ) : users.length ? users.map((u) => {
                                const [cls, label] = roleBadge(u.role);
                                return (
                                    <tr key={u.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4 font-medium text-gray-900">{u.name}</td>
                                        <td className="px-6 py-4 text-gray-600">{u.email}</td>
                                        <td className="px-6 py-4"><span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}`}>{label}</span></td>
                                        <td className="px-6 py-4">
                                            {u.status === 'active' ? (
                                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                            ) : (
                                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Deactivated</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-gray-500">{formatDate(u.created_at)}</td>
                                        <td className="px-6 py-4 text-right">
                                            {u.can_manage ? (
                                                u.status === 'active' ? (
                                                    <button type="button" onClick={() => setStatus(u, false)} className="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition">Deactivate</button>
                                                ) : (
                                                    <button type="button" onClick={() => setStatus(u, true)} className="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 hover:text-green-900 hover:bg-green-50 rounded-md transition">Activate</button>
                                                )
                                            ) : (
                                                <span className="text-xs text-gray-300">&mdash;</span>
                                            )}
                                        </td>
                                    </tr>
                                );
                            }) : (
                                <tr><td colSpan={6} className="px-6 py-12 text-center text-gray-400">No users found.</td></tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {data?.meta && <Pagination meta={data.meta} onPage={(p) => setSearchParams(cleanParams({ search, role, page: p }))} />}
        </div>
    );
}
