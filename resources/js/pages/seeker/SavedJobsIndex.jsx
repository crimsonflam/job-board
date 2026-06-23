import { useCallback, useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { seekerApi } from '../../api';
import { useMeta } from '../../hooks/useMeta';
import JobCard from '../../components/JobCard';
import JobFilters from '../../components/JobFilters';
import Pagination from '../../components/Pagination';
import { FullPageLoader } from '../../components/guards';
import { cleanParams } from '../../utils';

const ARRAY_KEYS = ['type', 'experience'];
const SCALAR_KEYS = ['search', 'location', 'education', 'salary_min', 'salary_max', 'category'];

function readFilters(searchParams) {
    const filters = {};
    SCALAR_KEYS.forEach((k) => {
        const v = searchParams.get(k);
        if (v !== null) filters[k] = v;
    });
    ARRAY_KEYS.forEach((k) => {
        const v = searchParams.getAll(k);
        if (v.length) filters[k] = v;
    });
    return filters;
}

/* Port of resources/views/seeker/saved-jobs/index.blade.php — same shared
   filters as Browse Jobs, scoped to bookmarked jobs. Unsaving removes the card. */
export default function SavedJobsIndex() {
    const meta = useMeta();
    const [searchParams, setSearchParams] = useSearchParams();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [filtersOpen, setFiltersOpen] = useState(false);

    const filters = readFilters(searchParams);
    const page = searchParams.get('page') || '1';

    const fetchJobs = useCallback(async () => {
        setLoading(true);
        try {
            const { data: res } = await seekerApi.savedJobs({ ...readFilters(searchParams), page });
            setData(res);
        } finally {
            setLoading(false);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [searchParams]);

    useEffect(() => { fetchJobs(); }, [fetchJobs]);

    const applyFilters = (next) => { setSearchParams(cleanParams(next)); setFiltersOpen(false); };
    const clearFilters = () => setSearchParams({});
    const goToPage = (p) => { setSearchParams(cleanParams({ ...filters, page: p })); window.scrollTo({ top: 0, behavior: 'smooth' }); };

    const toggleSaved = async (job) => {
        await seekerApi.toggleSaved(job.id);
        // Unsaving on this page removes the card from the saved list.
        setData((d) => ({
            ...d,
            data: d.data.filter((j) => j.id !== job.id),
            meta: d.meta ? { ...d.meta, total: Math.max(0, d.meta.total - 1) } : d.meta,
        }));
    };

    const activeFilterCount = ['search', 'type', 'location', 'education', 'experience', 'salary_min', 'salary_max']
        .filter((k) => {
            const v = filters[k];
            return Array.isArray(v) ? v.length > 0 : v !== undefined && v !== '';
        }).length;

    const jobs = data?.data ?? [];
    const total = data?.meta?.total ?? 0;

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-900">Saved Jobs</h1>
                <p className="mt-1 text-gray-500 text-sm">{total} saved {total === 1 ? 'job' : 'jobs'}</p>
            </div>

            <div className="lg:hidden mb-4">
                <button type="button" onClick={() => setFiltersOpen((o) => !o)} className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M3 4.5h18M6 12h12M10 19.5h4" /></svg>
                    Filters
                    {activeFilterCount > 0 && <span className="ml-2 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-primary-600 text-white">{activeFilterCount}</span>}
                </button>
            </div>

            <div className="flex flex-col lg:flex-row gap-8">
                <aside className={`w-full lg:w-72 flex-shrink-0 lg:block ${filtersOpen ? 'block' : 'hidden'}`}>
                    <JobFilters meta={meta} filters={filters} onApply={applyFilters} onClear={clearFilters} />
                </aside>

                <div className="flex-1 min-w-0">
                    {loading ? (
                        <FullPageLoader />
                    ) : jobs.length ? (
                        <>
                            <div className="space-y-4">
                                {jobs.map((job) => <JobCard key={job.id} job={{ ...job, is_saved: true }} onToggleSaved={toggleSaved} />)}
                            </div>
                            <Pagination meta={data.meta} onPage={goToPage} />
                        </>
                    ) : (
                        <div className="bg-white border border-gray-200 rounded-xl p-12 text-center">
                            <svg className="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                            {activeFilterCount > 0 ? (
                                <>
                                    <h3 className="text-lg font-semibold text-gray-900">No saved jobs match your filters</h3>
                                    <button onClick={clearFilters} className="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Clear filters</button>
                                </>
                            ) : (
                                <>
                                    <h3 className="text-lg font-semibold text-gray-900">You haven't saved any jobs yet.</h3>
                                    <Link to="/jobs" className="inline-block mt-4 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Browse Jobs</Link>
                                </>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
