import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { seekerApi } from '../../api';
import { FullPageLoader } from '../../components/guards';
import { formatDate } from '../../utils';

/* Port of resources/views/seeker/applications/show.blade.php. */
export default function SeekerApplicationShow() {
    const { id } = useParams();
    const [app, setApp] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        seekerApi.application(id)
            .then(({ data }) => setApp(data.data))
            .finally(() => setLoading(false));
    }, [id]);

    if (loading) return <FullPageLoader />;
    if (!app) return null;

    const meta = app.status === 'accepted'
        ? ['bg-green-100 text-green-800', 'Accepted ✓']
        : app.status === 'rejected'
            ? ['bg-red-100 text-red-800', 'Rejected ✗']
            : ['bg-gray-100 text-gray-700', 'No Response Yet'];

    return (
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="mb-6">
                <Link to="/seeker/applications" className="text-sm text-primary-600 hover:text-primary-700">&larr; Back to Applications</Link>
            </div>

            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <div className="flex items-start justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-gray-900">{app.job_listing?.title}</h1>
                        <p className="mt-1 text-gray-600">{app.job_listing?.company?.company_name}</p>
                        <p className="mt-1 text-sm text-gray-400">Applied on {formatDate(app.created_at)}</p>
                    </div>
                    <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${meta[0]}`}>{meta[1]}</span>
                </div>

                {app.has_response && app.response_message && (
                    <div className="mt-4 bg-gray-50 border border-gray-100 rounded-lg p-4">
                        <h3 className="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Employer's Response</h3>
                        <p className="text-sm text-gray-700 whitespace-pre-line">{app.status === 'accepted' && '🎉 '}{app.response_message}</p>
                        {app.responded_at && <p className="mt-2 text-xs text-gray-400">Replied on {formatDate(app.responded_at)}</p>}
                    </div>
                )}
            </div>

            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-3">CV Submitted</h2>
                {app.has_resume ? (
                    <div className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div className="flex items-center min-w-0">
                            <svg className="h-8 w-8 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <div className="ml-3 min-w-0">
                                <span className="block text-sm text-gray-700 truncate">{app.resume_file_name ?? 'Resume'}</span>
                                <span className="block text-xs text-gray-400">{app.cv_is_default ? 'Default CV' : 'Custom CV for this job'}</span>
                            </div>
                        </div>
                        <a href={app.resume_url} target="_blank" rel="noopener noreferrer" className="flex-shrink-0 inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 bg-primary-50 rounded-md hover:bg-primary-100">
                            <svg className="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Download
                        </a>
                    </div>
                ) : (
                    <p className="text-gray-500 text-sm italic">No CV was attached to this application.</p>
                )}
            </div>

            <div className="flex items-center space-x-4">
                <Link to="/seeker/applications" className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Back to Applications</Link>
            </div>
        </div>
    );
}
