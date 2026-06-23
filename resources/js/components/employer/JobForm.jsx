import { useState } from 'react';
import { Link } from 'react-router-dom';

/*
 * Shared job create/edit form (ports employer/jobs/create.blade.php and
 * edit.blade.php — identical fields/rules). Job type "remote" disables and
 * clears the location field, exactly like the Alpine x-model logic. `errors`
 * is the flat string[] from a failed submit; the parent owns the API call.
 */
const EMPTY = {
    title: '', category_id: '', description: '', requirements: '', benefits: '',
    type: '', experience_level: '', education_level: 'none', location: '',
    salary_min: '', salary_max: '', skills: '',
};

export default function JobForm({ meta, initial, onSubmit, submitting, errors = [], heading, subtitle, submitLabel }) {
    const [form, setForm] = useState({ ...EMPTY, ...initial });
    const set = (key, value) => setForm((f) => ({ ...f, [key]: value }));

    const isRemote = form.type === 'remote';

    const submit = (e) => {
        e.preventDefault();
        // Remote jobs carry no location (server enforces this too).
        onSubmit({ ...form, location: isRemote ? '' : form.location });
    };

    const categories = meta?.categories ?? [];
    const cities = meta?.cities ?? [];
    const typeLabels = meta?.job_types ?? {};
    const experienceLabels = meta?.experience_levels ?? {};
    const educationLabels = meta?.education_levels ?? {};

    const inputClass = 'w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm';

    return (
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-gray-900">{heading}</h1>
                <p className="mt-1 text-sm text-gray-500">{subtitle}</p>
            </div>

            {errors.length > 0 && (
                <div className="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 className="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                    <ul className="list-disc list-inside text-sm text-red-700 space-y-1">
                        {errors.map((e, i) => <li key={i}>{e}</li>)}
                    </ul>
                </div>
            )}

            <form onSubmit={submit} className="space-y-6">
                {/* Basic info */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                    <h2 className="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Basic Information</h2>

                    <div>
                        <label htmlFor="title" className="block text-sm font-medium text-gray-700 mb-1">Job Title <span className="text-red-500">*</span></label>
                        <input type="text" id="title" value={form.title} onChange={(e) => set('title', e.target.value)} required className={inputClass} placeholder="Job Title" />
                    </div>

                    <div>
                        <label htmlFor="category_id" className="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category_id" value={form.category_id ?? ''} onChange={(e) => set('category_id', e.target.value)} className={inputClass}>
                            <option value="">Select a category</option>
                            {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                        </select>
                    </div>

                    <div>
                        <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-1">Description <span className="text-red-500">*</span></label>
                        <textarea id="description" rows={6} required value={form.description} onChange={(e) => set('description', e.target.value)} className={inputClass} placeholder="Job Description" />
                    </div>

                    <div>
                        <label htmlFor="requirements" className="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
                        <textarea id="requirements" rows={4} value={form.requirements ?? ''} onChange={(e) => set('requirements', e.target.value)} className={inputClass} placeholder="Requirements" />
                    </div>

                    <div>
                        <label htmlFor="benefits" className="block text-sm font-medium text-gray-700 mb-1">Benefits</label>
                        <textarea id="benefits" rows={4} value={form.benefits ?? ''} onChange={(e) => set('benefits', e.target.value)} className={inputClass} placeholder="Benefits" />
                    </div>
                </div>

                {/* Job details */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                    <h2 className="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Job Details</h2>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label htmlFor="type" className="block text-sm font-medium text-gray-700 mb-1">Job Type <span className="text-red-500">*</span></label>
                            <select id="type" required value={form.type} onChange={(e) => set('type', e.target.value)} className={inputClass}>
                                <option value="">Select type</option>
                                {Object.entries(typeLabels).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                            </select>
                        </div>

                        <div>
                            <label htmlFor="experience_level" className="block text-sm font-medium text-gray-700 mb-1">Experience Level <span className="text-red-500">*</span></label>
                            <select id="experience_level" required value={form.experience_level} onChange={(e) => set('experience_level', e.target.value)} className={inputClass}>
                                <option value="">Select level</option>
                                {Object.entries(experienceLabels).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label htmlFor="education_level" className="block text-sm font-medium text-gray-700 mb-1">Education Requirement <span className="text-red-500">*</span></label>
                            <select id="education_level" required value={form.education_level} onChange={(e) => set('education_level', e.target.value)} className={inputClass}>
                                {Object.entries(educationLabels).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                            </select>
                        </div>

                        <div>
                            <label htmlFor="location" className="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select id="location" value={isRemote ? '' : (form.location ?? '')} disabled={isRemote} onChange={(e) => set('location', e.target.value)} className={`${inputClass} ${isRemote ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''}`}>
                                <option value="">Select a city</option>
                                {cities.map((city) => <option key={city} value={city}>{city}</option>)}
                            </select>
                            {isRemote && <p className="mt-1 text-xs text-gray-500">This job is remote — location not required.</p>}
                        </div>
                    </div>
                </div>

                {/* Compensation */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                    <h2 className="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Compensation</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label htmlFor="salary_min" className="block text-sm font-medium text-gray-700 mb-1">Salary Min (MAD)</label>
                            <input type="number" id="salary_min" min="0" value={form.salary_min ?? ''} onChange={(e) => set('salary_min', e.target.value)} className={inputClass} placeholder="Minimum salary (MAD)" />
                        </div>
                        <div>
                            <label htmlFor="salary_max" className="block text-sm font-medium text-gray-700 mb-1">Salary Max (MAD)</label>
                            <input type="number" id="salary_max" min="0" value={form.salary_max ?? ''} onChange={(e) => set('salary_max', e.target.value)} className={inputClass} placeholder="Maximum salary (MAD)" />
                        </div>
                    </div>
                </div>

                {/* Skills */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <label htmlFor="skills" className="block text-sm font-medium text-gray-700 mb-1">Skills</label>
                    <input type="text" id="skills" value={form.skills ?? ''} onChange={(e) => set('skills', e.target.value)} className={inputClass} placeholder="Skills (comma separated)" />
                    <p className="mt-1 text-xs text-gray-500">Separate skills with commas.</p>
                </div>

                <div className="flex items-center justify-end space-x-3">
                    <Link to="/employer/jobs" className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</Link>
                    <button type="submit" disabled={submitting} className="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition disabled:opacity-60">
                        {submitting ? 'Saving…' : submitLabel}
                    </button>
                </div>
            </form>
        </div>
    );
}
