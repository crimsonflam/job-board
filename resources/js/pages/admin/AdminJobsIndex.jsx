import { useCallback, useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { adminApi } from '../../api';
import Pagination from '../../components/Pagination';
import { FullPageLoader } from '../../components/guards';
import { formatDate } from '../../utils';

/* Port of resources/views/admin/jobs/index.blade.php — view + delete only. */
export default function AdminJobsIndex() {
    const [searchParams, setSearchParams] = useSearchParams();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [searchInput, setSearchInput] = useState(searchParams.get('search') || '');

    const search = searchParams.get('search') || '';
    const page = searchParams.get('page') || '1';

    const fetchJobs = useCallback(async () => {
        setLoading(true);
        try {
            const params = {};
            if (search) params.search = search;
            params.page = page;
            const { data: res } = await adminApi.jobs(params);
            setData(res);
        } finally {
            setLoading(false);
        }
    }, [search, page]);

    useEffect(() => { fetchJobs(); }, [fetchJobs]);
    useEffect(() => { setSearchInput(search); }, [search]);

    const submitSearch = (e) => {
        e.preventDefault();
        setSearchParams(searchInput ? { search: searchInput } : {});
    };

    const remove = async (job) => {
        if (!window.confirm('Are you sure you want to delete this job posting? This action cannot be undone.')) return;
        await adminApi.deleteJob(job.id);
        setData((d) => ({ ...d, data: d.data.filter((j) => j.id !== job.id) }));
    };

    const jobs = data?.data ?? [];

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="flex items-center justify-between mb-8">
                <h1 className="text-2xl font-bold text-gray-900">Manage Jobs</h1>
                <Link to="/admin/dashboard" className="text-sm text-primary-600 hover:text-primary-800 font-medium">Back to Dashboard</Link>
            </div>

            <div className="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                <form onSubmit={submitSearch} className="flex flex-col sm:flex-row gap-4">
                    <div className="flex-1">
                        <input type="text" value={searchInput} onChange={(e) => setSearchInput(e.target.value)} placeholder="Search jobs..." className="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-2 border" />
                    </div>
                    <button type="submit" className="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Search</button>
                    {search && <button type="button" onClick={() => setSearchParams({})} className="inline-flex items-center px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</button>}
                </form>
            </div>

            <div className="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Title</th>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Company</th>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Category</th>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                                <th className="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                                <th className="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {loading ? (
                                <tr><td colSpan={6} className="px-6 py-12"><FullPageLoader /></td></tr>
                            ) : jobs.length ? jobs.map((job) => (
                                <tr key={job.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4"><div className="font-medium text-gray-900">{job.title?.slice(0, 35)}</div></td>
                                    <td className="px-6 py-4 text-gray-600">{job.company?.company_name ?? 'N/A'}</td>
                                    <td className="px-6 py-4 text-gray-600">{job.category?.name ?? 'N/A'}</td>
                                    <td className="px-6 py-4">
                                        {job.status === 'active' ? (
                                            <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                        ) : (
                                            <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Inactive</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-gray-500">{formatDate(job.created_at)}</td>
                                    <td className="px-6 py-4 text-right">
                                        <button type="button" onClick={() => remove(job)} className="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white rounded-md transition" style={{ backgroundColor: '#8b0000' }} onMouseOver={(e) => { e.currentTarget.style.backgroundColor = '#6b1b1b'; }} onMouseOut={(e) => { e.currentTarget.style.backgroundColor = '#8b0000'; }}>
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            )) : (
                                <tr><td colSpan={6} className="px-6 py-12 text-center text-gray-400">No job listings found.</td></tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {data?.meta && <Pagination meta={data.meta} onPage={(p) => setSearchParams(search ? { search, page: p } : { page: p })} />}
        </div>
    );
}
