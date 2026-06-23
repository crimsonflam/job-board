import { useCallback, useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { employerApi } from '../../api';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import { FullPageLoader } from '../../components/guards';
import { allErrors, cleanParams, formatDate } from '../../utils';

const noSpace = (text) => (text || '').replace(/\s/g, '').length;

/* Port of resources/views/employer/applicants.blade.php — unified applicants
   screen with shared CV + Reply modals (Alpine state → React state). */
export default function EmployerApplicants() {
    const [searchParams, setSearchParams] = useSearchParams();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);

    // Toolbar draft (applied on submit), seeded from the URL.
    const [toolbar, setToolbar] = useState({
        search: searchParams.get('search') || '',
        job: searchParams.get('job') || '',
        status: searchParams.get('status') || 'all',
        sort: searchParams.get('sort') || 'newest',
    });

    const [cv, setCv] = useState({ open: false, name: '', file: '', url: '', isDefault: true, applied: '' });
    const [reply, setReply] = useState({ open: false, id: null, name: '', decision: 'accepted', message: '' });
    const [replyErrors, setReplyErrors] = useState([]);
    const [replySubmitting, setReplySubmitting] = useState(false);

    const params = {
        search: searchParams.get('search') || '',
        job: searchParams.get('job') || '',
        status: searchParams.get('status') || 'all',
        sort: searchParams.get('sort') || 'newest',
        page: searchParams.get('page') || '1',
    };

    const fetchApplicants = useCallback(async () => {
        setLoading(true);
        try {
            const { data: res } = await employerApi.applicants(cleanParams(params));
            setData(res);
        } finally {
            setLoading(false);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [searchParams]);

    useEffect(() => { fetchApplicants(); }, [fetchApplicants]);

    // Sync toolbar inputs if the URL changes (e.g. arriving with ?job=ID).
    useEffect(() => {
        setToolbar({
            search: searchParams.get('search') || '',
            job: searchParams.get('job') || '',
            status: searchParams.get('status') || 'all',
            sort: searchParams.get('sort') || 'newest',
        });
    }, [searchParams]);

    const applyToolbar = (e) => {
        e.preventDefault();
        setSearchParams(cleanParams({ ...toolbar }));
    };

    const reset = () => setSearchParams({});

    const setTb = (k, v) => setToolbar((t) => ({ ...t, [k]: v }));

    const openCv = (app) => {
        setCv({
            open: true,
            name: app.user?.name ?? '',
            file: app.resume_file_name ?? 'Resume.pdf',
            url: app.has_resume ? employerApi.cvDownloadUrl(app.id) : '',
            isDefault: !!app.cv_is_default,
            applied: formatDate(app.created_at),
        });
    };

    const openReply = (app) => {
        setReplyErrors([]);
        setReply({ open: true, id: app.id, name: app.user?.name ?? '', decision: 'accepted', message: '' });
    };

    const submitReply = async (e) => {
        e.preventDefault();
        setReplySubmitting(true);
        setReplyErrors([]);
        try {
            const { data: res } = await employerApi.updateApplicationStatus(reply.id, {
                status: reply.decision,
                response_message: reply.message,
            });
            const updated = res.data;
            setData((d) => ({
                ...d,
                data: d.data.map((a) => (a.id === reply.id ? { ...a, status: updated.status, response_message: updated.response_message, has_response: updated.has_response } : a)),
            }));
            setReply((r) => ({ ...r, open: false }));
        } catch (err) {
            setReplyErrors(allErrors(err));
        } finally {
            setReplySubmitting(false);
        }
    };

    const statusMeta = (status) => {
        if (status === 'accepted') return ['bg-green-100 text-green-800', 'Accepted'];
        if (status === 'rejected') return ['bg-red-100 text-red-800', 'Rejected'];
        return ['bg-gray-100 text-gray-700', 'Awaiting Reply'];
    };

    const apps = data?.data ?? [];
    const jobs = data?.jobs ?? [];
    const total = data?.meta?.total ?? 0;
    const isFiltered = params.search || params.job || params.status !== 'all' || params.sort !== 'newest';

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-900">Applicants</h1>
                <p className="mt-1 text-gray-500 text-sm">{total} {total === 1 ? 'applicant' : 'applicants'} across your jobs.</p>
            </div>

            {/* Toolbar */}
            <form onSubmit={applyToolbar} className="bg-white border border-gray-200 rounded-xl p-4 mb-6 flex flex-wrap items-end gap-3">
                <div className="flex-1 min-w-[180px]">
                    <label htmlFor="search" className="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" id="search" value={toolbar.search} onChange={(e) => setTb('search', e.target.value)} placeholder="Search applicants..." className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                </div>
                <div className="min-w-[160px]">
                    <label htmlFor="job" className="block text-xs font-medium text-gray-500 mb-1">Job</label>
                    <select id="job" value={toolbar.job} onChange={(e) => setTb('job', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Jobs</option>
                        {jobs.map((j) => <option key={j.id} value={j.id}>{j.title}</option>)}
                    </select>
                </div>
                <div className="min-w-[150px]">
                    <label htmlFor="status" className="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select id="status" value={toolbar.status} onChange={(e) => setTb('status', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="all">All</option>
                        <option value="pending">Awaiting Reply</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div className="min-w-[150px]">
                    <label htmlFor="sort" className="block text-xs font-medium text-gray-500 mb-1">Sort by</label>
                    <select id="sort" value={toolbar.sort} onChange={(e) => setTb('sort', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="newest">Date (newest)</option>
                        <option value="oldest">Date (oldest)</option>
                        <option value="status">By Status</option>
                    </select>
                </div>
                <button type="submit" className="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Apply</button>
                {isFiltered && <button type="button" onClick={reset} className="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">Reset</button>}
            </form>

            {loading ? (
                <FullPageLoader />
            ) : apps.length ? (
                <>
                    {apps.map((app) => {
                        const [cls, label] = statusMeta(app.status);
                        return (
                            <div key={app.id} className="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-4">
                                <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div className="min-w-0">
                                        <h3 className="text-base font-semibold text-gray-900">{app.user?.name}</h3>
                                        <p className="text-sm text-gray-500">{app.job_listing?.title}</p>
                                        <p className="text-xs text-gray-400 mt-0.5">Applied on {formatDate(app.created_at)}</p>
                                        <span className={`mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${cls}`}>{label}</span>
                                    </div>
                                    <div className="flex flex-wrap items-center gap-2 flex-shrink-0">
                                        <button type="button" onClick={() => openCv(app)} className="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                            <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                            Open CV
                                        </button>
                                        <button type="button" onClick={() => openReply(app)} className="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">Reply</button>
                                    </div>
                                </div>

                                {app.has_response && app.response_message && (
                                    <div className="mt-3 pt-3 border-t border-gray-100">
                                        <p className="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Your response</p>
                                        <p className="text-sm text-gray-700 line-clamp-2">{app.response_message}</p>
                                    </div>
                                )}
                            </div>
                        );
                    })}
                    <Pagination meta={data.meta} onPage={(p) => setSearchParams(cleanParams({ ...params, page: p }))} />
                </>
            ) : (
                <div className="bg-white border border-gray-200 rounded-xl p-12 text-center">
                    <svg className="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <h3 className="mt-4 text-base font-semibold text-gray-900">No applicants found</h3>
                    <p className="mt-1 text-sm text-gray-500">Applicants will appear here once candidates apply, or try clearing your filters.</p>
                </div>
            )}

            {/* CV modal */}
            <Modal open={cv.open} onClose={() => setCv((c) => ({ ...c, open: false }))} maxWidth="max-w-md">
                <div className="flex items-center justify-between pb-4 border-b border-gray-200 -mx-6 px-6 -mt-6 pt-5">
                    <h3 className="text-lg font-semibold text-gray-900">Applicant CV</h3>
                    <button type="button" onClick={() => setCv((c) => ({ ...c, open: false }))} className="text-gray-400 hover:text-primary-600">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div className="py-5">
                    <div className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                        <svg className="h-9 w-9 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        <div className="min-w-0">
                            <p className="text-sm font-medium text-gray-900 truncate">{cv.file}</p>
                            <p className="text-xs text-gray-500">{cv.isDefault ? 'Default CV' : 'Custom CV for this job'} · Applied {cv.applied}</p>
                        </div>
                    </div>
                    {!cv.url && <p className="mt-4 text-sm text-gray-500 italic">No CV file is attached to this application.</p>}
                </div>
                <div className="pt-4 border-t border-gray-200 -mx-6 px-6 flex items-center justify-end gap-3">
                    <button type="button" onClick={() => setCv((c) => ({ ...c, open: false }))} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
                    {cv.url && (
                        <a href={cv.url} className="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                            <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Download CV
                        </a>
                    )}
                </div>
            </Modal>

            {/* Reply modal */}
            <Modal open={reply.open} onClose={() => setReply((r) => ({ ...r, open: false }))} maxWidth="max-w-lg">
                <form onSubmit={submitReply}>
                    <div className="flex items-center justify-between pb-4 border-b border-gray-200 -mx-6 px-6 -mt-6 pt-5">
                        <h3 className="text-lg font-semibold text-gray-900">Reply to <span>{reply.name}</span></h3>
                        <button type="button" onClick={() => setReply((r) => ({ ...r, open: false }))} className="text-gray-400 hover:text-primary-600">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div className="py-5 space-y-4">
                        <div className="grid grid-cols-2 gap-3">
                            <label className={`border rounded-lg p-3 cursor-pointer text-center text-sm font-medium transition ${reply.decision === 'accepted' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'}`}>
                                <input type="radio" value="accepted" checked={reply.decision === 'accepted'} onChange={() => setReply((r) => ({ ...r, decision: 'accepted' }))} className="sr-only" />
                                Accept
                            </label>
                            <label className={`border rounded-lg p-3 cursor-pointer text-center text-sm font-medium transition ${reply.decision === 'rejected' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'}`}>
                                <input type="radio" value="rejected" checked={reply.decision === 'rejected'} onChange={() => setReply((r) => ({ ...r, decision: 'rejected' }))} className="sr-only" />
                                Reject
                            </label>
                        </div>

                        <div>
                            <label htmlFor="reply_message" className="block text-sm font-medium text-gray-700 mb-1">Message to Applicant</label>
                            <textarea
                                id="reply_message" rows={4} value={reply.message}
                                onChange={(e) => setReply((r) => ({ ...r, message: e.target.value }))}
                                placeholder={reply.decision === 'accepted' ? 'Share details about interview, start date, next steps...' : 'Thank you for applying. We appreciate your interest...'}
                                className="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                            />
                            <div className="mt-1 text-right text-xs text-gray-400">{noSpace(reply.message)}/500 characters</div>
                        </div>

                        {replyErrors.length > 0 && (
                            <div className="text-sm text-red-600">
                                {replyErrors.map((e, i) => <p key={i}>{e}</p>)}
                            </div>
                        )}
                    </div>

                    <div className="pt-4 border-t border-gray-200 -mx-6 px-6 flex items-center justify-end gap-3">
                        <button type="button" onClick={() => setReply((r) => ({ ...r, open: false }))} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" disabled={noSpace(reply.message) < 1 || replySubmitting} className={`px-5 py-2 text-sm font-medium text-white rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed ${reply.decision === 'accepted' ? 'bg-green-600 hover:bg-green-700' : 'bg-primary-600 hover:bg-primary-700'}`}>
                            {reply.decision === 'accepted' ? 'Send Acceptance' : 'Send Rejection'}
                        </button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}
