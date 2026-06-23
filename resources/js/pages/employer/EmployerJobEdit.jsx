import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { employerApi } from '../../api';
import { useMeta } from '../../hooks/useMeta';
import JobForm from '../../components/employer/JobForm';
import { FullPageLoader } from '../../components/guards';
import { allErrors } from '../../utils';

/* Port of resources/views/employer/jobs/edit.blade.php. */
export default function EmployerJobEdit() {
    const { id } = useParams();
    const meta = useMeta();
    const navigate = useNavigate();
    const [initial, setInitial] = useState(null);
    const [errors, setErrors] = useState([]);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        employerApi.job(id).then(({ data }) => {
            const j = data.data;
            setInitial({
                title: j.title ?? '',
                category_id: j.category_id ?? '',
                description: j.description ?? '',
                requirements: j.requirements ?? '',
                benefits: j.benefits ?? '',
                type: j.type ?? '',
                experience_level: j.experience_level ?? '',
                education_level: j.education_level ?? 'none',
                location: j.location ?? '',
                salary_min: j.salary_min ?? '',
                salary_max: j.salary_max ?? '',
                skills: Array.isArray(j.skills) ? j.skills.join(', ') : '',
            });
        });
    }, [id]);

    const submit = async (payload) => {
        setSubmitting(true);
        setErrors([]);
        try {
            await employerApi.updateJob(id, payload);
            navigate('/employer/jobs');
        } catch (err) {
            setErrors(allErrors(err));
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } finally {
            setSubmitting(false);
        }
    };

    if (!initial) return <FullPageLoader />;

    return (
        <JobForm
            meta={meta}
            initial={initial}
            onSubmit={submit}
            submitting={submitting}
            errors={errors}
            heading="Edit Job"
            subtitle="Update the details of your job listing."
            submitLabel="Save Changes"
        />
    );
}
