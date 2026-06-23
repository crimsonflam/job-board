import { useState } from 'react';
import { Link } from 'react-router-dom';
import { employerApi } from '../../api';
import { useAuth } from '../../contexts/AuthContext';
import { useMeta } from '../../hooks/useMeta';
import { allErrors, fieldErrors } from '../../utils';

/* Port of resources/views/employer/company/edit.blade.php. */
export default function EmployerCompanyEdit() {
    const { user, setUser } = useAuth();
    const meta = useMeta();
    const hasProfile = user?.has_company_profile;

    const [form, setForm] = useState({
        company_name: user?.company_name ?? '',
        company_description: user?.company_description ?? '',
        company_website: user?.company_website ?? '',
        company_location: user?.company_location ?? '',
        industry: user?.industry ?? '',
    });
    const [errors, setErrors] = useState([]);
    const [fields, setFields] = useState({});
    const [submitting, setSubmitting] = useState(false);

    const set = (key, value) => setForm((f) => ({ ...f, [key]: value }));

    const submit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setErrors([]);
        setFields({});
        try {
            const { data } = await employerApi.updateCompany(form);
            setUser(data.data);
        } catch (err) {
            setErrors(allErrors(err));
            setFields(fieldErrors(err));
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } finally {
            setSubmitting(false);
        }
    };

    const cities = meta?.cities ?? [];
    const inputClass = 'w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm';

    return (
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-gray-900">{hasProfile ? 'Edit Company Profile' : 'Set Up Your Company Profile'}</h1>
                <p className="mt-1 text-sm text-gray-500">{hasProfile ? 'Keep your company information up to date.' : 'Complete your company profile to start posting jobs.'}</p>
            </div>

            {errors.length > 0 && (
                <div className="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div className="flex items-center mb-2">
                        <svg className="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" /></svg>
                        <h3 className="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    </div>
                    <ul className="list-disc list-inside text-sm text-red-700 space-y-1">
                        {errors.map((e, i) => <li key={i}>{e}</li>)}
                    </ul>
                </div>
            )}

            <form onSubmit={submit} className="space-y-6">
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                    <h2 className="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Basic Information</h2>

                    <div>
                        <label htmlFor="company_name" className="block text-sm font-medium text-gray-700 mb-1">Company Name <span className="text-red-500">*</span></label>
                        <input type="text" id="company_name" value={form.company_name} onChange={(e) => set('company_name', e.target.value)} required maxLength={255} className={inputClass} placeholder="Company Name" />
                        {fields.company_name && <p className="mt-1 text-sm text-red-600">{fields.company_name[0]}</p>}
                    </div>

                    <div>
                        <label htmlFor="company_description" className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="company_description" rows={5} value={form.company_description ?? ''} onChange={(e) => set('company_description', e.target.value)} className={inputClass} placeholder="Company Description" />
                        {fields.company_description && <p className="mt-1 text-sm text-red-600">{fields.company_description[0]}</p>}
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label htmlFor="company_website" className="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="url" id="company_website" value={form.company_website ?? ''} onChange={(e) => set('company_website', e.target.value)} className={inputClass} placeholder="Website URL" />
                            {fields.company_website && <p className="mt-1 text-sm text-red-600">{fields.company_website[0]}</p>}
                        </div>

                        <div>
                            <label htmlFor="company_location" className="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select id="company_location" value={form.company_location ?? ''} onChange={(e) => set('company_location', e.target.value)} className={inputClass}>
                                <option value="">Select a city</option>
                                {cities.map((city) => <option key={city} value={city}>{city}</option>)}
                            </select>
                            {fields.company_location && <p className="mt-1 text-sm text-red-600">{fields.company_location[0]}</p>}
                        </div>
                    </div>

                    <div>
                        <label htmlFor="industry" className="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                        <input type="text" id="industry" value={form.industry ?? ''} onChange={(e) => set('industry', e.target.value)} className={inputClass} placeholder="Industry" />
                        {fields.industry && <p className="mt-1 text-sm text-red-600">{fields.industry[0]}</p>}
                    </div>
                </div>

                <div className="flex items-center justify-end space-x-3">
                    <Link to="/employer/dashboard" className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</Link>
                    <button type="submit" disabled={submitting} className="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition disabled:opacity-60">
                        {submitting ? 'Saving…' : (hasProfile ? 'Save Changes' : 'Create Company Profile')}
                    </button>
                </div>
            </form>
        </div>
    );
}
