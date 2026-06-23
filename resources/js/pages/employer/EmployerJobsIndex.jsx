import { useCallback, useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { employerApi } from '../../api';
import Pagination from '../../components/Pagination';
import { FullPageLoader } from '../../components/guards';
import { formatDate } from '../../utils';

/* Port of resources/views/employer/jobs/index.blade.php — "My Jobs". */
export default function EmployerJobsIndex() {
    const [searchParams, setSearchParams] = useSearchParams();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const page = searchParams.get('page') || '1';

    const fetchJobs = useCallback(async () => {
        setLoading(true);
        try {
            const { data: res } = await employerApi.jobs({ page });
            setData(res);
        } finally {
            setLoading(false);
        }
    }, [page]);

    useEffect(() => { fetchJobs(); }, [fetchJobs]);

    const toggleStatus = async (job) => {
        const { data } = await employerApi.toggleJobStatus(job.id);
        const updated = data.data;
        setData((d) => ({ ...d, data: d.data.map((j) => (j.id === job.id ? { ...j, status: updated.status } : j)) }));
    };

    const remove = async (job) => {
        if (!window.confirm(`Delete “${job.title}”? This also removes its applications and cannot be undone.`)) return;
        await employerApi.deleteJob(job.id);
        setData((d) => ({ ...d, data: d.data.filter((j) => j.id !== job.id) }));
    };

    const jobs = data?.data ?? [];

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <h1 className="text-2xl font-bold text-gray-900">My Jobs</h1>
                <Link to="/employer/jobs/create" className="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" /></svg>
                    Post New Job
                </Link>
            </div>

            {loading ? (
                <FullPageLoader />
            ) : jobs.length ? (
                <>
                    {jobs.map((job) => {
                        const isActive = job.status === 'active';
                        return (
                            <div key={job.id} className="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-4">
                                <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div className="min-w-0">
                                        <div className="flex items-center gap-3">
                                            <h3 className="text-lg font-semibold text-gray-900 truncate">{job.title}</h3>
                                            <span className={`flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'}`}>
                                                {isActive ? 'Active' : 'Inactive'}
                                            </span>
                                        </div>
                                        <p className="mt-1 text-sm text-gray-500">Posted {formatDate(job.created_at)}</p>
                                        <Link to={`/employer/applicants?job=${job.id}`} className="mt-2 inline-flex items-center text-sm text-gray-600 hover:text-primary-600">
                                            <span className="text-lg font-bold text-primary-600 mr-1.5">{job.applications_count ?? 0}</span>
                                            {(job.applications_count ?? 0) === 1 ? 'applicant' : 'applicants'}
                                        </Link>
                                    </div>

                                    <div className="flex flex-wrap items-center gap-2 flex-shrink-0">
                                        <button type="button" onClick={() => toggleStatus(job)} className={`px-3 py-1.5 text-sm font-medium rounded-lg border transition ${isActive ? 'border-gray-300 text-gray-700 hover:bg-gray-50' : 'border-green-300 text-green-700 hover:bg-green-50'}`}>
                                            {isActive ? 'Set Inactive' : 'Set Active'}
                                        </button>
                                        <Link to={`/employer/applicants?job=${job.id}`} className="px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">View Applicants</Link>
                                        <Link to={`/employer/jobs/${job.id}/edit`} className="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Edit</Link>
                                        <button type="button" onClick={() => remove(job)} className="px-3 py-1.5 text-sm font-medium text-white rounded-lg transition" style={{ backgroundColor: '#8b0000' }} onMouseOver={(e) => { e.currentTarget.style.backgroundColor = '#6b1b1b'; }} onMouseOut={(e) => { e.currentTarget.style.backgroundColor = '#8b0000'; }}>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                    <Pagination meta={data.meta} onPage={(p) => setSearchParams({ page: p })} />
                </>
            ) : (
                <div className="bg-white border border-gray-200 rounded-xl p-12 text-center">
                    <svg className="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <h3 className="mt-4 text-base font-semibold text-gray-900">No job listings yet</h3>
                    <p className="mt-1 text-sm text-gray-500">Get started by posting your first job.</p>
                    <Link to="/employer/jobs/create" className="inline-block mt-4 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Post New Job</Link>
                </div>
            )}
        </div>
    );
}
