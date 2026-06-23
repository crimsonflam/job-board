import { useCallback, useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { jobsApi, seekerApi } from '../../api';
import { useMeta } from '../../hooks/useMeta';
import JobCard from '../../components/JobCard';
import JobFilters from '../../components/JobFilters';
import Pagination from '../../components/Pagination';
import { FullPageLoader } from '../../components/guards';
import { cleanParams } from '../../utils';

const ARRAY_KEYS = ['type', 'experience'];
const SCALAR_KEYS = ['search', 'location', 'education', 'salary_min', 'salary_max', 'category'];

/* Read the filter object out of the URL search params. */
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

/*
 * Port of resources/views/jobs/index.blade.php — Browse Jobs. Filters live in
 * the URL (like withQueryString()), so navigating back/forward and reloading
 * keeps the active filters and page.
 */
export default function JobsIndex() {
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
            const params = { ...readFilters(searchParams), page };
            const { data: res } = await jobsApi.list(params);
            setData(res);
        } finally {
            setLoading(false);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [searchParams]);

    useEffect(() => {
        fetchJobs();
    }, [fetchJobs]);

    const applyFilters = (next) => {
        // Reset to page 1 whenever filters change.
        setSearchParams(cleanParams(next));
        setFiltersOpen(false);
    };

    const clearFilters = () => setSearchParams({});

    const goToPage = (p) => {
        const next = cleanParams({ ...filters, page: p });
        setSearchParams(next);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const toggleSaved = async (job) => {
        const { data: res } = await seekerApi.toggleSaved(job.id);
        setData((d) => ({
            ...d,
            data: d.data.map((j) => (j.id === job.id ? { ...j, is_saved: res.saved } : j)),
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
                <h1 className="text-2xl font-bold text-gray-900">Browse Jobs</h1>
                <p className="mt-1 text-gray-500 text-sm">
                    Showing {jobs.length} {jobs.length === 1 ? 'job' : 'jobs'} out of {total}
                </p>
            </div>

            {/* Mobile filter toggle */}
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
                                {jobs.map((job) => (
                                    <JobCard key={job.id} job={job} onToggleSaved={toggleSaved} />
                                ))}
                            </div>
                            <Pagination meta={data.meta} onPage={goToPage} />
                        </>
                    ) : (
                        <div className="bg-white border border-gray-200 rounded-xl p-12 text-center">
                            <svg className="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            <h3 className="text-lg font-semibold text-gray-900">No jobs match your criteria</h3>
                            <p className="mt-1 text-sm text-gray-500">Try adjusting your filters.</p>
                            <button onClick={clearFilters} className="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Clear all filters</button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
