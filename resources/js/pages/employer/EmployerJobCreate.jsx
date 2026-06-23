import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { employerApi } from '../../api';
import { useMeta } from '../../hooks/useMeta';
import JobForm from '../../components/employer/JobForm';
import { allErrors } from '../../utils';

/* Port of resources/views/employer/jobs/create.blade.php. */
export default function EmployerJobCreate() {
    const meta = useMeta();
    const navigate = useNavigate();
    const [errors, setErrors] = useState([]);
    const [submitting, setSubmitting] = useState(false);

    const submit = async (payload) => {
        setSubmitting(true);
        setErrors([]);
        try {
            await employerApi.createJob(payload);
            navigate('/employer/jobs');
        } catch (err) {
            setErrors(allErrors(err));
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <JobForm
            meta={meta}
            initial={{}}
            onSubmit={submit}
            submitting={submitting}
            errors={errors}
            heading="Post New Job"
            subtitle="Fill in the details below. Your job is published immediately."
            submitLabel="Publish Job"
        />
    );
}
