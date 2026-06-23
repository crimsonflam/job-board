import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { jobsApi, seekerApi } from '../../api';
import { useAuth } from '../../contexts/AuthContext';
import Modal from '../../components/Modal';
import { FullPageLoader } from '../../components/guards';
import { allErrors, formatDate } from '../../utils';

/* Port of resources/views/jobs/show.blade.php — single job detail + apply modal. */
export default function JobShow() {
    const { slug } = useParams();
    const { user } = useAuth();
    const navigate = useNavigate();
    const [job, setJob] = useState(null);
    const [myApplication, setMyApplication] = useState(null);
    const [loading, setLoading] = useState(true);
    const [notFound, setNotFound] = useState(false);

    const [applyOpen, setApplyOpen] = useState(false);
    const [cvChoice, setCvChoice] = useState('default');
    const [resumeFile, setResumeFile] = useState(null);
    const [modalErrors, setModalErrors] = useState([]);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        let active = true;
        setLoading(true);
        jobsApi.show(slug)
            .then(({ data }) => {
                if (!active) return;
                setJob(data.data);
                setMyApplication(data.meta?.my_application ?? null);
            })
            .catch((err) => {
                if (active && err.response?.status === 404) setNotFound(true);
            })
            .finally(() => active && setLoading(false));
        return () => { active = false; };
    }, [slug]);

    const isSeeker = user?.is_seeker;

    const toggleSaved = async () => {
        const { data } = await seekerApi.toggleSaved(job.id);
        setJob((j) => ({ ...j, is_saved: data.saved }));
    };

    const submitApply = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setModalErrors([]);
        const fd = new FormData();
        fd.append('cv_choice', cvChoice);
        if (cvChoice === 'upload' && resumeFile) fd.append('resume', resumeFile);
        try {
            await seekerApi.apply(job.id, fd);
            navigate('/seeker/applications');
        } catch (err) {
            setModalErrors(allErrors(err));
        } finally {
            setSubmitting(false);
        }
    };

    if (loading) return <FullPageLoader />;
    if (notFound || !job) {
        return (
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
                <h1 className="text-2xl font-bold text-gray-900">Job not found</h1>
                <Link to="/jobs" className="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Back to Jobs</Link>
            </div>
        );
    }

    const company = job.company ?? {};
    const initials = (company.company_name ?? 'J').substring(0, 2).toUpperCase();

    const appliedBadge = myApplication
        ? (myApplication.status === 'accepted'
            ? ['bg-green-100 text-green-800', 'Accepted']
            : myApplication.status === 'rejected'
                ? ['bg-red-100 text-red-800', 'Rejected']
                : ['bg-gray-100 text-gray-700', 'Applied'])
        : null;

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {/* Breadcrumb */}
            <nav className="mb-6 text-sm text-gray-500 flex items-center">
                <Link to="/jobs" className="inline-flex items-center hover:text-primary-600">
                    <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" /></svg>
                    Back to Jobs
                </Link>
                {job.category && (
                    <>
                        <span className="mx-2">/</span>
                        <Link to={`/jobs?category=${job.category.id}`} className="hover:text-primary-600">{job.category.name}</Link>
                    </>
                )}
            </nav>

            {/* Company header */}
            <div className="bg-white border border-gray-200 rounded-xl overflow-hidden mb-8">
                <div className="h-40 bg-primary-600" />
                <div className="p-6 -mt-10 relative">
                    <div className="flex flex-col sm:flex-row sm:items-end sm:space-x-5">
                        <div className="flex-shrink-0 w-20 h-20 bg-white rounded-xl border-4 border-white shadow-sm flex items-center justify-center">
                            <span className="text-2xl font-bold text-gray-400">{initials}</span>
                        </div>
                        <div className="mt-4 sm:mt-0 sm:pb-1">
                            {company.company_name && <span className="text-sm text-primary-600 font-medium">{company.company_name}</span>}
                            <h1 className="text-2xl font-bold text-gray-900 mt-1">{job.title}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div className="flex flex-col lg:flex-row gap-8">
                {/* Main content */}
                <div className="flex-1 min-w-0 space-y-8">
                    <div className="bg-white border border-gray-200 rounded-xl p-6">
                        <div className="flex flex-wrap gap-4 text-sm">
                            <span className="inline-flex items-center px-3 py-1 rounded-full bg-primary-50 text-primary-700 font-medium">{job.type_label}</span>
                            <span className="inline-flex items-center text-gray-600">
                                <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
                                {job.education_label}
                            </span>
                            <span className="inline-flex items-center text-gray-600">
                                <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172" /></svg>
                                {job.experience_label}
                            </span>
                            <span className="inline-flex items-center text-gray-600">
                                <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                {job.is_remote ? 'Remote' : (job.location ?? 'Not specified')}
                            </span>
                            <span className="inline-flex items-center text-gray-600">
                                <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {job.salary_range}
                            </span>
                        </div>
                    </div>

                    <div className="bg-white border border-gray-200 rounded-xl p-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">Job Description</h2>
                        <div className="prose prose-sm max-w-none text-gray-600" dangerouslySetInnerHTML={{ __html: job.description ?? '' }} />
                    </div>

                    {job.requirements && (
                        <div className="bg-white border border-gray-200 rounded-xl p-6">
                            <h2 className="text-lg font-semibold text-gray-900 mb-4">Requirements</h2>
                            <div className="prose prose-sm max-w-none text-gray-600" dangerouslySetInnerHTML={{ __html: job.requirements }} />
                        </div>
                    )}

                    {job.benefits && (
                        <div className="bg-white border border-gray-200 rounded-xl p-6">
                            <h2 className="text-lg font-semibold text-gray-900 mb-4">Benefits</h2>
                            <div className="prose prose-sm max-w-none text-gray-600" dangerouslySetInnerHTML={{ __html: job.benefits }} />
                        </div>
                    )}

                    {job.skills && job.skills.length > 0 && (
                        <div className="bg-white border border-gray-200 rounded-xl p-6">
                            <h2 className="text-lg font-semibold text-gray-900 mb-4">Skills</h2>
                            <div className="flex flex-wrap gap-2">
                                {job.skills.map((skill, i) => (
                                    <span key={i} className="inline-flex items-center px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-sm font-medium">{skill}</span>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                {/* Sidebar */}
                <aside className="w-full lg:w-80 flex-shrink-0 space-y-6">
                    <div className="bg-white border border-gray-200 rounded-xl p-6 space-y-3">
                        {!user && (
                            <Link to="/login" className="block w-full py-2.5 px-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition text-sm text-center">
                                Sign in to Apply
                            </Link>
                        )}

                        {isSeeker && (
                            <>
                                {myApplication ? (
                                    <>
                                        <div className="text-center">
                                            <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${appliedBadge[0]}`}>{appliedBadge[1]}</span>
                                            <p className="mt-2 text-xs text-gray-500">Applied on {formatDate(myApplication.created_at)}</p>
                                        </div>
                                        {myApplication.has_response ? (
                                            <Link to="/seeker/applications" className="block w-full py-2 px-4 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm text-center">View Response</Link>
                                        ) : (
                                            <div className="w-full py-2 px-4 bg-gray-50 text-gray-500 font-medium rounded-lg text-xs text-center">No response from the employer yet</div>
                                        )}
                                    </>
                                ) : (
                                    <button type="button" onClick={() => { setApplyOpen(true); setModalErrors([]); }} className="block w-full py-2.5 px-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition text-sm text-center">
                                        Apply for This Job
                                    </button>
                                )}

                                <button type="button" onClick={toggleSaved} className="w-full py-2.5 px-4 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm flex items-center justify-center">
                                    {job.is_saved ? (
                                        <>
                                            <svg className="w-4 h-4 mr-2 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" /></svg>
                                            Saved
                                        </>
                                    ) : (
                                        <>
                                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                                            Save Job
                                        </>
                                    )}
                                </button>
                            </>
                        )}
                    </div>

                    {/* Company info card */}
                    {company.company_name && (
                        <div className="bg-white border border-gray-200 rounded-xl p-6">
                            <h3 className="text-sm font-semibold text-gray-900 mb-4">About the Company</h3>
                            <div className="flex items-center space-x-3 mb-4">
                                <div className="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <span className="text-lg font-bold text-gray-400">{company.company_name.substring(0, 2).toUpperCase()}</span>
                                </div>
                                <div>
                                    <span className="text-sm font-medium text-gray-900">{company.company_name}</span>
                                    {company.industry && <p className="text-xs text-gray-500">{company.industry}</p>}
                                </div>
                            </div>
                            {company.company_description && <p className="text-sm text-gray-600 mb-3">{company.company_description}</p>}
                            {company.company_location && (
                                <p className="text-sm text-gray-500 mb-2">
                                    <svg className="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                    {company.company_location}
                                </p>
                            )}
                            {company.company_website && (
                                <a href={company.company_website} target="_blank" rel="noopener noreferrer" className="block mt-4 text-center text-sm text-primary-600 font-medium hover:text-primary-700">
                                    Visit Website
                                </a>
                            )}
                        </div>
                    )}
                </aside>
            </div>

            {/* Apply modal (seeker, not yet applied) */}
            {isSeeker && !myApplication && (
                <Modal open={applyOpen} onClose={() => setApplyOpen(false)} maxWidth="max-w-lg">
                    <div className="flex items-center justify-between pb-4 border-b border-gray-200 -mx-6 px-6 -mt-6 pt-5">
                        <h3 className="text-lg font-semibold text-gray-900">Submit Your Application</h3>
                        <button type="button" onClick={() => setApplyOpen(false)} className="text-gray-400 hover:text-primary-600">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {!user.has_default_resume ? (
                        <div className="py-8 text-center">
                            <div className="mx-auto w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center mb-4">
                                <svg className="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                            </div>
                            <h4 className="text-base font-semibold text-gray-900">Upload your resume first</h4>
                            <p className="mt-1 text-sm text-gray-500">Please upload your resume in your profile before applying.</p>
                            <Link to="/seeker/profile" className="inline-block mt-5 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Go to Profile</Link>
                        </div>
                    ) : (
                        <form onSubmit={submitApply}>
                            <div className="py-5 space-y-4">
                                <label className={`block border rounded-lg p-4 cursor-pointer transition ${cvChoice === 'default' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'}`}>
                                    <div className="flex items-start">
                                        <input type="radio" name="cv_choice" value="default" checked={cvChoice === 'default'} onChange={() => setCvChoice('default')} className="mt-1 w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500" />
                                        <div className="ml-3">
                                            <span className="block text-sm font-medium text-gray-900">Use My Default CV</span>
                                            <span className="block text-sm text-gray-600 mt-0.5">{user.resume_file_name ?? 'My Resume'}</span>
                                            {user.resume_uploaded_at && <span className="block text-xs text-gray-400 mt-0.5">Uploaded on {formatDate(user.resume_uploaded_at)}</span>}
                                            <span className="block text-xs text-gray-400 mt-1">This CV will be used for this application.</span>
                                        </div>
                                    </div>
                                </label>

                                <label className={`block border rounded-lg p-4 cursor-pointer transition ${cvChoice === 'upload' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'}`}>
                                    <div className="flex items-start">
                                        <input type="radio" name="cv_choice" value="upload" checked={cvChoice === 'upload'} onChange={() => setCvChoice('upload')} className="mt-1 w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500" />
                                        <div className="ml-3 flex-1">
                                            <span className="block text-sm font-medium text-gray-900">Upload New CV for This Role</span>
                                            <span className="block text-xs text-gray-400 mt-1">This CV is used only for this job. Your default CV stays unchanged.</span>
                                            {cvChoice === 'upload' && (
                                                <div className="mt-3">
                                                    <input type="file" name="resume" accept=".pdf" onChange={(e) => setResumeFile(e.target.files[0] ?? null)} className="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                                                    <p className="mt-1 text-xs text-gray-400">PDF only, max 5MB.</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </label>

                                {modalErrors.length > 0 && (
                                    <div className="text-sm text-red-600">
                                        {modalErrors.map((e, i) => <p key={i}>{e}</p>)}
                                    </div>
                                )}
                            </div>

                            <div className="pt-4 border-t border-gray-200 -mx-6 px-6 flex items-center justify-end space-x-3">
                                <button type="button" onClick={() => setApplyOpen(false)} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                                <button type="submit" disabled={submitting} className="px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition disabled:opacity-60">
                                    {submitting ? 'Submitting…' : 'Submit Application'}
                                </button>
                            </div>
                        </form>
                    )}
                </Modal>
            )}
        </div>
    );
}
