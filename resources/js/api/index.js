import client from './client';
import { withBase } from '../config';

//this file contains all the API calls used in the frontend
export const authApi = {
    me: () => client.get('/me'),
    login: (payload) => client.post('/login', payload),
    register: (payload) => client.post('/register', payload),
    logout: () => client.post('/logout'),
};

export const metaApi = {
    get: () => client.get('/meta'),
};

export const jobsApi = {
    list: (params) => client.get('/jobs', { params }),
    show: (slug) => client.get(`/jobs/${slug}`),
};

export const seekerApi = {
    dashboard: () => client.get('/seeker/dashboard'),
    profile: () => client.get('/seeker/profile'),
    updateProfile: (formData) =>
        client.post('/seeker/profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),
    deleteCv: () => client.delete('/seeker/cv'),
    cvDownloadUrl: () => withBase('/api/seeker/cv/download'),

    applications: (params) => client.get('/seeker/applications', { params }),
    application: (id) => client.get(`/seeker/applications/${id}`),
    apply: (jobId, formData) =>
        client.post(`/seeker/apply/${jobId}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),

    savedJobs: (params) => client.get('/seeker/saved-jobs', { params }),
    toggleSaved: (jobId) => client.post(`/seeker/saved-jobs/${jobId}`),
};

export const employerApi = {
    dashboard: () => client.get('/employer/dashboard'),
    company: () => client.get('/employer/company'),
    updateCompany: (payload) => client.put('/employer/company', payload),

    jobs: (params) => client.get('/employer/jobs', { params }),
    job: (id) => client.get(`/employer/jobs/${id}`),
    createJob: (payload) => client.post('/employer/jobs', payload),
    updateJob: (id, payload) => client.put(`/employer/jobs/${id}`, payload),
    deleteJob: (id) => client.delete(`/employer/jobs/${id}`),
    toggleJobStatus: (id) => client.put(`/employer/jobs/${id}/toggle-status`),

    applicants: (params) => client.get('/employer/applicants', { params }),
    updateApplicationStatus: (id, payload) =>
        client.put(`/employer/applications/${id}/status`, payload),
    cvDownloadUrl: (id) => withBase(`/api/employer/applications/${id}/cv`),
};

export const adminApi = {
    dashboard: () => client.get('/admin/dashboard'),
    jobs: (params) => client.get('/admin/jobs', { params }),
    deleteJob: (id) => client.delete(`/admin/jobs/${id}`),
    users: (params) => client.get('/admin/users', { params }),
    deactivateUser: (id) => client.put(`/admin/users/${id}/deactivate`),
    activateUser: (id) => client.put(`/admin/users/${id}/activate`),
};
