import { useState } from 'react';
import { Link } from 'react-router-dom';
import { seekerApi } from '../../api';
import { useAuth } from '../../contexts/AuthContext';
import { useFlash } from '../../contexts/FlashContext';
import { allErrors, fieldErrors, formatDate } from '../../utils';

/* Port of resources/views/seeker/profile/edit.blade.php — personal info form +
   a separate CV upload/download/delete card (bio counter via React state). */
export default function SeekerProfileEdit() {
    const { user, setUser } = useAuth();
    const { flash } = useFlash();

    const [form, setForm] = useState({
        name: user?.name ?? '',
        email: user?.email ?? '',
        phone: user?.phone ?? '',
        website: user?.website ?? '',
        bio: user?.bio ?? '',
    });
    const [errors, setErrors] = useState([]);
    const [fields, setFields] = useState({});
    const [resumeFile, setResumeFile] = useState(null);
    const [savingInfo, setSavingInfo] = useState(false);
    const [savingCv, setSavingCv] = useState(false);

    const set = (key, value) => setForm((f) => ({ ...f, [key]: value }));

    // Persist via the shared update endpoint. `withResume` includes the chosen
    // PDF; otherwise only the text fields are sent (the server requires them).
    const persist = async (withResume) => {
        const fd = new FormData();
        Object.entries(form).forEach(([k, v]) => fd.append(k, v ?? ''));
        if (withResume && resumeFile) fd.append('resume', resumeFile);
        const { data } = await seekerApi.updateProfile(fd);
        setUser(data.data);
        return data.data;
    };

    const saveInfo = async (e) => {
        e.preventDefault();
        setSavingInfo(true);
        setErrors([]);
        setFields({});
        try {
            await persist(false);
        } catch (err) {
            setErrors(allErrors(err));
            setFields(fieldErrors(err));
        } finally {
            setSavingInfo(false);
        }
    };

    const saveCv = async (e) => {
        e.preventDefault();
        if (!resumeFile) return;
        setSavingCv(true);
        setErrors([]);
        setFields({});
        try {
            await persist(true);
            setResumeFile(null);
            e.target.reset();
        } catch (err) {
            setErrors(allErrors(err));
            setFields(fieldErrors(err));
        } finally {
            setSavingCv(false);
        }
    };

    const deleteCv = async () => {
        if (!window.confirm('Delete your CV? You will need to upload one again before applying.')) return;
        const { data } = await seekerApi.deleteCv();
        setUser(data.data);
    };

    const downloadCv = () => {
        window.location.href = seekerApi.cvDownloadUrl();
    };

    const inputClass = 'w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm';

    return (
        <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-gray-900">Edit Profile</h1>
                <p className="mt-1 text-sm text-gray-500">Keep your details up to date. Your CV is required before you can apply to jobs.</p>
            </div>

            {errors.length > 0 && (
                <div className="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 className="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                    <ul className="list-disc list-inside text-sm text-red-700 space-y-1">
                        {errors.map((e, i) => <li key={i}>{e}</li>)}
                    </ul>
                </div>
            )}

            {/* Personal info */}
            <form onSubmit={saveInfo} className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">Full Name <span className="text-red-500">*</span></label>
                    <input type="text" id="name" value={form.name} onChange={(e) => set('name', e.target.value)} required maxLength={100} className={inputClass} />
                </div>
                <div>
                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">Email <span className="text-red-500">*</span></label>
                    <input type="email" id="email" value={form.email} onChange={(e) => set('email', e.target.value)} required className={inputClass} />
                </div>
                <div>
                    <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-1">Phone Number <span className="text-red-500">*</span></label>
                    <input type="text" id="phone" value={form.phone} onChange={(e) => set('phone', e.target.value)} required maxLength={20} className={inputClass} placeholder="Phone Number" />
                </div>
                <div>
                    <label htmlFor="website" className="block text-sm font-medium text-gray-700 mb-1">Website <span className="text-gray-400 font-normal">(optional)</span></label>
                    <input type="url" id="website" value={form.website} onChange={(e) => set('website', e.target.value)} className={inputClass} placeholder="Website URL" />
                </div>
                <div>
                    <label htmlFor="bio" className="block text-sm font-medium text-gray-700 mb-1">Bio <span className="text-gray-400 font-normal">(optional)</span></label>
                    <textarea id="bio" rows={4} maxLength={250} value={form.bio} onChange={(e) => set('bio', e.target.value)} className={inputClass} placeholder="Bio" />
                    <p className="mt-1 text-right text-xs text-gray-400">{form.bio.length}/250</p>
                </div>
                <div className="flex items-center justify-end space-x-3">
                    <Link to="/seeker/dashboard" className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</Link>
                    <button type="submit" disabled={savingInfo} className="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition disabled:opacity-60">
                        {savingInfo ? 'Saving…' : 'Save'}
                    </button>
                </div>
            </form>

            {/* CV section */}
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-1">CV / Resume</h2>
                <p className="text-sm text-gray-500 mb-4">PDF only, max 5MB. Required before applying to jobs.</p>

                {user?.has_default_resume && (
                    <div className="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4">
                        <div className="flex items-center min-w-0">
                            <svg className="h-9 w-9 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <div className="ml-3 min-w-0">
                                <p className="text-sm font-medium text-green-700 flex items-center">
                                    <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    CV uploaded successfully
                                </p>
                                <p className="text-sm text-gray-700 truncate">{user.resume_file_name ?? 'Resume.pdf'}</p>
                                {user.resume_uploaded_at && <p className="text-xs text-gray-400">Uploaded on {formatDate(user.resume_uploaded_at)}</p>}
                            </div>
                        </div>
                        <div className="flex items-center gap-2 flex-shrink-0">
                            <button type="button" onClick={downloadCv} className="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                Download
                            </button>
                            <button type="button" onClick={deleteCv} className="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50">Delete</button>
                        </div>
                    </div>
                )}

                <form onSubmit={saveCv}>
                    <label className="block text-sm font-medium text-gray-700 mb-1">{user?.has_default_resume ? 'Replace CV' : 'Upload CV'}</label>
                    <input type="file" accept="application/pdf" required onChange={(e) => setResumeFile(e.target.files[0] ?? null)} className="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                    {fields.resume && <p className="mt-1 text-sm text-red-600">{fields.resume[0]}</p>}
                    <button type="submit" disabled={savingCv} className="mt-3 px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition disabled:opacity-60">
                        {savingCv ? 'Uploading…' : (user?.has_default_resume ? 'Replace CV' : 'Upload CV')}
                    </button>
                </form>
            </div>
        </div>
    );
}
