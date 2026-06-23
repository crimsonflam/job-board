import { useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { allErrors, fieldErrors } from '../utils';

/* Port of resources/views/auth/login.blade.php. */
export default function Login() {
    const { login } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const [form, setForm] = useState({ email: '', password: '', remember: false });
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
            await login(form);
            const to = location.state?.from?.pathname ?? '/dashboard';
            navigate(to, { replace: true });
        } catch (err) {
            setErrors(allErrors(err));
            setFields(fieldErrors(err));
        } finally {
            setSubmitting(false);
        }
    };

    const inputClass = (field) =>
        `w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm ${fields[field] ? 'border-red-500' : ''}`;

    return (
        <div className="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div className="w-full max-w-md">
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">Welcome back</h1>
                    <p className="mt-2 text-gray-600">Sign in to your account to continue</p>
                </div>

                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    {errors.length > 0 && (
                        <div className="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <ul className="list-disc list-inside text-sm text-red-700 space-y-1">
                                {errors.map((e, i) => <li key={i}>{e}</li>)}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" value={form.email} onChange={(e) => set('email', e.target.value)} required autoFocus className={inputClass('email')} placeholder="Email Address" />
                            {fields.email && <p className="mt-1 text-sm text-red-600">{fields.email[0]}</p>}
                        </div>

                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" id="password" value={form.password} onChange={(e) => set('password', e.target.value)} required className={inputClass('password')} placeholder="Password" />
                            {fields.password && <p className="mt-1 text-sm text-red-600">{fields.password[0]}</p>}
                        </div>

                        <div className="flex items-center justify-between">
                            <label className="flex items-center">
                                <input type="checkbox" checked={form.remember} onChange={(e) => set('remember', e.target.checked)} className="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                <span className="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                        </div>

                        <button type="submit" disabled={submitting} className="w-full py-2.5 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition text-sm disabled:opacity-60">
                            {submitting ? 'Signing in…' : 'Sign In'}
                        </button>
                    </form>
                </div>

                <p className="mt-6 text-center text-sm text-gray-600">
                    Don't have an account?{' '}
                    <Link to="/register" className="text-indigo-600 font-medium hover:text-indigo-500">Create one</Link>
                </p>
            </div>
        </div>
    );
}
