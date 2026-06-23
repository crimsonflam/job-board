import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { allErrors, fieldErrors } from '../utils';

/* Port of resources/views/auth/register.blade.php (role toggle + conditional
   company name field via React state instead of Alpine). */
export default function Register() {
    const { register } = useAuth();
    const navigate = useNavigate();
    const [form, setForm] = useState({
        name: '', email: '', password: '', password_confirmation: '', role: 'seeker', company_name: '',
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
            await register(form);
            navigate('/dashboard', { replace: true });
        } catch (err) {
            setErrors(allErrors(err));
            setFields(fieldErrors(err));
        } finally {
            setSubmitting(false);
        }
    };

    const inputClass = (field) =>
        `w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm ${fields[field] ? 'border-red-500' : ''}`;

    const roleCard = (value, label, iconPath) => {
        const selected = form.role === value;
        return (
            <label className="relative cursor-pointer" onClick={() => set('role', value)}>
                <input type="radio" name="role" value={value} checked={selected} onChange={() => set('role', value)} className="sr-only peer" />
                <div className={`border-2 rounded-lg p-4 text-center transition ${selected ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'}`}>
                    <svg className={`w-8 h-8 mx-auto mb-2 ${selected ? 'text-indigo-600' : 'text-gray-400'}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d={iconPath} />
                    </svg>
                    <span className={`text-sm font-medium ${selected ? 'text-indigo-700' : 'text-gray-700'}`}>{label}</span>
                </div>
            </label>
        );
    };

    return (
        <div className="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div className="w-full max-w-md">
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">Create your account</h1>
                    <p className="mt-2 text-gray-600">Join thousands of professionals finding their next opportunity</p>
                </div>

                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    {errors.length > 0 && (
                        <div className="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div className="flex items-center mb-2">
                                <svg className="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clipRule="evenodd" /></svg>
                                <span className="text-sm font-medium text-red-800">Please fix the following errors:</span>
                            </div>
                            <ul className="list-disc list-inside text-sm text-red-700 space-y-1">
                                {errors.map((e, i) => <li key={i}>{e}</li>)}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="name" value={form.name} onChange={(e) => set('name', e.target.value)} required autoFocus className={inputClass('name')} placeholder="Full Name" />
                            {fields.name && <p className="mt-1 text-sm text-red-600">{fields.name[0]}</p>}
                        </div>

                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" value={form.email} onChange={(e) => set('email', e.target.value)} required className={inputClass('email')} placeholder="Email Address" />
                            {fields.email && <p className="mt-1 text-sm text-red-600">{fields.email[0]}</p>}
                        </div>

                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" id="password" value={form.password} onChange={(e) => set('password', e.target.value)} required className={inputClass('password')} placeholder="Password" />
                            {fields.password && <p className="mt-1 text-sm text-red-600">{fields.password[0]}</p>}
                        </div>

                        <div>
                            <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" id="password_confirmation" value={form.password_confirmation} onChange={(e) => set('password_confirmation', e.target.value)} required className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Confirm Password" />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-3">I want to</label>
                            <div className="grid grid-cols-2 gap-3">
                                {roleCard('seeker', 'Job Seeker', 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z')}
                                {roleCard('employer', 'Employer', 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21')}
                            </div>
                            {fields.role && <p className="mt-1 text-sm text-red-600">{fields.role[0]}</p>}
                        </div>

                        {form.role === 'employer' && (
                            <div>
                                <label htmlFor="company_name" className="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                <input type="text" id="company_name" value={form.company_name} onChange={(e) => set('company_name', e.target.value)} className={inputClass('company_name')} placeholder="Company Name" />
                                {fields.company_name && <p className="mt-1 text-sm text-red-600">{fields.company_name[0]}</p>}
                            </div>
                        )}

                        <button type="submit" disabled={submitting} className="w-full py-2.5 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition text-sm disabled:opacity-60">
                            {submitting ? 'Creating account…' : 'Create Account'}
                        </button>
                    </form>
                </div>

                <p className="mt-6 text-center text-sm text-gray-600">
                    Already have an account?{' '}
                    <Link to="/login" className="text-indigo-600 font-medium hover:text-indigo-500">Sign in</Link>
                </p>
            </div>
        </div>
    );
}
