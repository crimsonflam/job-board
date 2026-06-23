import { useEffect, useState } from 'react';

/*
 * Port of resources/views/jobs/_filters.blade.php — the shared filter sidebar
 * for Browse Jobs and Saved Jobs. Holds a local draft of the filters and calls
 * onApply(draft) on submit (the parent then re-queries, preserving the filters
 * in the URL). `meta` supplies the job-type / experience / education label maps
 * and the city list.
 */
const FILTER_KEYS = ['search', 'type', 'location', 'education', 'experience', 'salary_min', 'salary_max'];

function countActive(filters) {
    return FILTER_KEYS.filter((k) => {
        const v = filters[k];
        if (Array.isArray(v)) return v.length > 0;
        return v !== undefined && v !== null && v !== '';
    }).length;
}

const EMPTY = { search: '', type: [], location: '', experience: [], education: '', salary_min: '', salary_max: '' };

export default function JobFilters({ meta, filters, onApply, onClear }) {
    const [draft, setDraft] = useState({ ...EMPTY, ...filters });

    // Keep the draft in sync if the applied filters change externally (e.g. clear).
    useEffect(() => {
        setDraft({ ...EMPTY, ...filters });
    }, [filters]);

    const activeCount = countActive(filters);

    const set = (key, value) => setDraft((d) => ({ ...d, [key]: value }));

    const toggleArray = (key, value) =>
        setDraft((d) => {
            const arr = d[key] ?? [];
            return {
                ...d,
                [key]: arr.includes(value) ? arr.filter((v) => v !== value) : [...arr, value],
            };
        });

    const submit = (e) => {
        e.preventDefault();
        onApply(draft);
    };

    const jobTypes = meta?.job_types ?? {};
    const experienceLevels = meta?.experience_levels ?? {};
    const educationLevels = meta?.education_levels ?? {};
    const cities = meta?.cities ?? [];

    const inputClass =
        'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500';

    return (
        <form onSubmit={submit} className="bg-white border border-gray-200 rounded-xl p-5 space-y-6">
            <div className="flex items-center justify-between">
                <h2 className="text-sm font-semibold text-gray-900">
                    Filters
                    {activeCount > 0 && <span className="ml-1 text-xs font-medium text-primary-600">({activeCount} active)</span>}
                </h2>
                {activeCount > 0 && (
                    <button type="button" onClick={onClear} className="text-xs text-gray-500 hover:text-primary-600">Clear all</button>
                )}
            </div>

            {/* Search */}
            <div>
                <label htmlFor="search" className="block text-sm font-medium text-gray-700 mb-1.5">Search</label>
                <div className="relative">
                    <span className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </span>
                    <input
                        type="text" id="search" value={draft.search}
                        onChange={(e) => set('search', e.target.value)}
                        placeholder="Job title, company..."
                        className="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    />
                </div>
            </div>

            {/* Job Type */}
            <div>
                <h3 className="text-sm font-medium text-gray-700 mb-2">Job Type</h3>
                <div className="space-y-2">
                    {Object.entries(jobTypes).map(([value, label]) => (
                        <label key={value} className="flex items-center">
                            <input
                                type="checkbox" value={value}
                                checked={(draft.type ?? []).includes(value)}
                                onChange={() => toggleArray('type', value)}
                                className="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                            />
                            <span className="ml-2 text-sm text-gray-600">{label}</span>
                        </label>
                    ))}
                </div>
            </div>

            {/* Location */}
            <div>
                <label htmlFor="location" className="block text-sm font-medium text-gray-700 mb-1.5">Location</label>
                <select id="location" value={draft.location} onChange={(e) => set('location', e.target.value)} className={inputClass}>
                    <option value="">All Cities</option>
                    {cities.map((city) => <option key={city} value={city}>{city}</option>)}
                </select>
            </div>

            {/* Work Experience */}
            <div>
                <h3 className="text-sm font-medium text-gray-700 mb-2">Work Experience</h3>
                <div className="space-y-2">
                    {Object.entries(experienceLevels).map(([value, label]) => (
                        <label key={value} className="flex items-center">
                            <input
                                type="checkbox" value={value}
                                checked={(draft.experience ?? []).includes(value)}
                                onChange={() => toggleArray('experience', value)}
                                className="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                            />
                            <span className="ml-2 text-sm text-gray-600">{label}</span>
                        </label>
                    ))}
                </div>
            </div>

            {/* Education Level */}
            <div>
                <label htmlFor="education" className="block text-sm font-medium text-gray-700 mb-1.5">Education Level</label>
                <select id="education" value={draft.education} onChange={(e) => set('education', e.target.value)} className={inputClass}>
                    <option value="">Any</option>
                    {Object.entries(educationLevels).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                </select>
            </div>

            {/* Salary Range */}
            <div>
                <h3 className="text-sm font-medium text-gray-700 mb-2">Salary Range (MAD)</h3>
                <div className="flex items-center gap-2">
                    <input type="number" value={draft.salary_min} onChange={(e) => set('salary_min', e.target.value)} placeholder="Min" min="0" className="w-1/2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                    <span className="text-gray-400">–</span>
                    <input type="number" value={draft.salary_max} onChange={(e) => set('salary_max', e.target.value)} placeholder="Max" min="0" className="w-1/2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                </div>
            </div>

            <div className="space-y-2 pt-2">
                <button type="submit" className="w-full py-2.5 px-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition text-sm">
                    Apply Filters
                </button>
                {activeCount > 0 && (
                    <button type="button" onClick={onClear} className="block w-full text-center text-sm text-gray-500 hover:text-gray-700">Reset filters</button>
                )}
            </div>
        </form>
    );
}
