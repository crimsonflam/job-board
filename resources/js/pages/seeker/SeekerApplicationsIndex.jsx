import { useCallback, useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { seekerApi } from '../../api';
import Pagination from '../../components/Pagination';
import { FullPageLoader } from '../../components/guards';
import { formatDate } from '../../utils';

/* Port of resources/views/seeker/applications/index.blade.php. */
function ApplicationCard({ application }) {
    const [expanded, setExpanded] = useState(false);

    const meta = application.status === 'accepted'
        ? ['bg-green-100 text-green-800', 'Accepted ✓']
        : application.status === 'rejected'
            ? ['bg-red-100 text-red-800', 'Rejected ✗']
            : ['bg-gray-100 text-gray-700', 'No Response Yet'];

    return (
        <div className="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-4">
            <div className="flex items-start justify-between gap-4">
                <div className="min-w-0">
                    <h3 className="text-base font-semibold text-gray-900 truncate">
                        <Link to={`/jobs/${application.job_listing?.slug}`} className="hover:text-primary-600">
                            {application.job_listing?.title}
                        </Link>
                    </h3>
                    <p className="text-sm font-medium text-gray-500">{application.job_listing?.company?.company_name ?? 'Company'}</p>
                    <p className="text-xs text-gray-400 mt-0.5">Applied on {formatDate(application.created_at)}</p>
                </div>
                <span className={`flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${meta[0]}`}>{meta[1]}</span>
            </div>

            {application.has_response && application.response_message && (
                <div className="mt-4">
                    <div className="bg-gray-50 border border-gray-100 rounded-lg p-3">
                        <p className={`text-sm text-gray-700 ${expanded ? '' : 'line-clamp-2'}`}>
                            {application.status === 'accepted' && '🎉 '}{application.response_message}
                        </p>
                        <button type="button" onClick={() => setExpanded((e) => !e)} className="mt-1 text-xs font-medium text-primary-600 hover:text-primary-700">
                            {expanded ? 'Show less' : 'Show more'}
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

export default function SeekerApplicationsIndex() {
    const [searchParams, setSearchParams] = useSearchParams();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);

    const status = searchParams.get('status') || 'all';
    const sort = searchParams.get('sort') || 'newest';
    const page = searchParams.get('page') || '1';

    const fetch = useCallback(async () => {
        setLoading(true);
        try {
            const { data: res } = await seekerApi.applications({ status, sort, page });
            setData(res);
        } finally {
            setLoading(false);
        }
    }, [status, sort, page]);

    useEffect(() => { fetch(); }, [fetch]);

    const update = (key, value) => {
        const next = { status, sort };
        next[key] = value;
        setSearchParams(next);
    };

    const goToPage = (p) => setSearchParams({ status, sort, page: p });

    const apps = data?.data ?? [];
    const total = data?.meta?.total ?? 0;

    return (
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-900">My Applications</h1>
                <p className="mt-1 text-gray-500 text-sm">Track the status of all your job applications.</p>
            </div>

            {total > 0 && (
                <div className="flex flex-wrap items-center gap-3 mb-6">
                    <div className="flex items-center gap-2">
                        <label htmlFor="status" className="text-sm text-gray-600">Status:</label>
                        <select id="status" value={status} onChange={(e) => update('status', e.target.value)} className="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="all">All Applications</option>
                            <option value="pending">No Response Yet</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div className="flex items-center gap-2">
                        <label htmlFor="sort" className="text-sm text-gray-600">Sort by:</label>
                        <select id="sort" value={sort} onChange={(e) => update('sort', e.target.value)} className="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="status">By Status</option>
                        </select>
                    </div>
                </div>
            )}

            {loading ? (
                <FullPageLoader />
            ) : apps.length ? (
                <>
                    {apps.map((app) => <ApplicationCard key={app.id} application={app} />)}
                    <div className="mt-6"><Pagination meta={data.meta} onPage={goToPage} /></div>
                </>
            ) : (
                <div className="bg-white border border-gray-200 rounded-xl p-12 text-center">
                    <svg className="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    {status !== 'all' ? (
                        <>
                            <h3 className="mt-3 text-base font-semibold text-gray-900">No {status} applications</h3>
                            <p className="mt-1 text-sm text-gray-500">Try a different status filter.</p>
                            <button onClick={() => setSearchParams({})} className="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Show all applications</button>
                        </>
                    ) : (
                        <>
                            <h3 className="mt-3 text-base font-semibold text-gray-900">You haven't applied to any jobs yet.</h3>
                            <Link to="/jobs" className="inline-block mt-4 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Browse Jobs</Link>
                        </>
                    )}
                </div>
            )}
        </div>
    );
}
