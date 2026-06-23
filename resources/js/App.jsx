import { useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate, useNavigate } from 'react-router-dom';

import { BASE_PATH } from './config';
import { FlashProvider, useFlash } from './contexts/FlashContext';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import { registerApiHandlers } from './api/client';

import Layout from './components/Layout';
import { RequireRole, RequireGuest, FullPageLoader } from './components/guards';

import Welcome from './pages/Welcome';
import Login from './pages/Login';
import Register from './pages/Register';
import JobsIndex from './pages/jobs/JobsIndex';
import JobShow from './pages/jobs/JobShow';

import SeekerDashboard from './pages/seeker/SeekerDashboard';
import SeekerProfileEdit from './pages/seeker/SeekerProfileEdit';
import SeekerApplicationsIndex from './pages/seeker/SeekerApplicationsIndex';
import SeekerApplicationShow from './pages/seeker/SeekerApplicationShow';
import SavedJobsIndex from './pages/seeker/SavedJobsIndex';

import EmployerDashboard from './pages/employer/EmployerDashboard';
import EmployerCompanyEdit from './pages/employer/EmployerCompanyEdit';
import EmployerJobsIndex from './pages/employer/EmployerJobsIndex';
import EmployerJobCreate from './pages/employer/EmployerJobCreate';
import EmployerJobEdit from './pages/employer/EmployerJobEdit';
import EmployerApplicants from './pages/employer/EmployerApplicants';

import AdminDashboard from './pages/admin/AdminDashboard';
import AdminJobsIndex from './pages/admin/AdminJobsIndex';
import AdminUsersIndex from './pages/admin/AdminUsersIndex';

//    error handling for api/ show flash msg for users
function ApiBridge() {
    const { flash } = useFlash();
    const { setUser } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        registerApiHandlers({
            flash: (type, text) => flash(type, text),
            onUnauthorized: () => {
                setUser(null);
                navigate('/login');
            },
        });
    }, [flash, setUser, navigate]);

    return null;
}

function HomeRoute() {
    const { user, loading } = useAuth();
    if (loading) return <FullPageLoader />;
    if (user) return <Navigate to="/dashboard" replace />;
    return <Welcome />;
}

function DashboardRedirect() {
    const { user, loading } = useAuth();
    if (loading) return <FullPageLoader />;
    if (!user) return <Navigate to="/login" replace />;
    if (user.is_admin) return <Navigate to="/admin/dashboard" replace />;
    if (user.is_employer) return <Navigate to="/employer/dashboard" replace />;
    return <Navigate to="/seeker/dashboard" replace />;
}

const SEEKER = ['seeker'];
const EMPLOYER = ['employer'];
const ADMIN = ['admin', 'super_admin'];

function AppRoutes() {
    return (
        <Routes>

            <Route path="/" element={<HomeRoute />} />
            <Route path="/jobs" element={<JobsIndex />} />
            <Route path="/jobs/:slug" element={<JobShow />} />

            <Route path="/login" element={<RequireGuest><Login /></RequireGuest>} />
            <Route path="/register" element={<RequireGuest><Register /></RequireGuest>} />

            <Route path="/dashboard" element={<DashboardRedirect />} />
            
            <Route path="/seeker/dashboard" element={<RequireRole roles={SEEKER}><SeekerDashboard /></RequireRole>} />
            <Route path="/seeker/profile" element={<RequireRole roles={SEEKER}><SeekerProfileEdit /></RequireRole>} />
            <Route path="/seeker/applications" element={<RequireRole roles={SEEKER}><SeekerApplicationsIndex /></RequireRole>} />
            <Route path="/seeker/applications/:id" element={<RequireRole roles={SEEKER}><SeekerApplicationShow /></RequireRole>} />
            <Route path="/seeker/saved-jobs" element={<RequireRole roles={SEEKER}><SavedJobsIndex /></RequireRole>} />

            <Route path="/employer/dashboard" element={<RequireRole roles={EMPLOYER} requireCompanyProfile><EmployerDashboard /></RequireRole>} />
            <Route path="/employer/company" element={<RequireRole roles={EMPLOYER}><EmployerCompanyEdit /></RequireRole>} />
            <Route path="/employer/jobs" element={<RequireRole roles={EMPLOYER} requireCompanyProfile><EmployerJobsIndex /></RequireRole>} />
            <Route path="/employer/jobs/create" element={<RequireRole roles={EMPLOYER} requireCompanyProfile><EmployerJobCreate /></RequireRole>} />
            <Route path="/employer/jobs/:id/edit" element={<RequireRole roles={EMPLOYER} requireCompanyProfile><EmployerJobEdit /></RequireRole>} />
            <Route path="/employer/applicants" element={<RequireRole roles={EMPLOYER} requireCompanyProfile><EmployerApplicants /></RequireRole>} />

            <Route path="/admin/dashboard" element={<RequireRole roles={ADMIN}><AdminDashboard /></RequireRole>} />
            <Route path="/admin/jobs" element={<RequireRole roles={ADMIN}><AdminJobsIndex /></RequireRole>} />
            <Route path="/admin/users" element={<RequireRole roles={ADMIN}><AdminUsersIndex /></RequireRole>} />

            <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
    );
}

export default function App() {
    return (
        <BrowserRouter basename={BASE_PATH || undefined}>
            <FlashProvider>
                <AuthProvider>
                    <ApiBridge />
                    <Layout>
                        <AppRoutes />
                    </Layout>
                </AuthProvider>
            </FlashProvider>
        </BrowserRouter>
    );
}
