<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Category;
use App\Models\JobListing;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'icon' => 'tech'],
            ['name' => 'Design', 'slug' => 'design', 'icon' => 'design'],
            ['name' => 'Marketing', 'slug' => 'marketing', 'icon' => 'marketing'],
            ['name' => 'Sales', 'slug' => 'sales', 'icon' => 'sales'],
            ['name' => 'Finance', 'slug' => 'finance', 'icon' => 'finance'],
            ['name' => 'Healthcare', 'slug' => 'healthcare', 'icon' => 'health'],
            ['name' => 'Education', 'slug' => 'education', 'icon' => 'education'],
            ['name' => 'Engineering', 'slug' => 'engineering', 'icon' => 'engineering'],
            ['name' => 'Customer Service', 'slug' => 'customer-service', 'icon' => 'support'],
            ['name' => 'Human Resources', 'slug' => 'human-resources', 'icon' => 'hr'],
        ];
        foreach ($categories as $cat) {
            Category::create($cat);
        }

        
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@jobboard.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@jobboard.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        
        
        $employers = [
            ['name' => 'John Smith', 'email' => 'employer@jobboard.com', 'company' => 'TechCorp Solutions', 'industry' => 'Technology', 'location' => 'Casablanca'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@designhub.com', 'company' => 'DesignHub Agency', 'industry' => 'Design', 'location' => 'Rabat'],
            ['name' => 'Mike Williams', 'email' => 'mike@cloudnine.com', 'company' => 'CloudNine Inc', 'industry' => 'Technology', 'location' => 'Marrakech'],
            ['name' => 'Emily Davis', 'email' => 'emily@healthplus.com', 'company' => 'HealthPlus Medical', 'industry' => 'Healthcare', 'location' => 'Tangier'],
            ['name' => 'Robert Brown', 'email' => 'robert@finserv.com', 'company' => 'FinServ Group', 'industry' => 'Finance', 'location' => 'Fes'],
        ];

        $employerModels = [];
        foreach ($employers as $i => $emp) {
            $employerModels[] = User::create([
                'name' => $emp['name'],
                'email' => $emp['email'],
                'password' => Hash::make('password'),
                'role' => 'employer',
                
                'status' => $i === count($employers) - 1 ? 'deactivated' : 'active',
                'email_verified_at' => now(),
                'company_name' => $emp['company'],
                'company_description' => "We are {$emp['company']}, a leading company in the {$emp['industry']} industry. We offer competitive salaries, great benefits, and a fantastic work culture.",
                'industry' => $emp['industry'],
                'company_location' => $emp['location'],
                'company_website' => 'https://' . Str::slug($emp['company']) . '.com',
            ]);
        }

        //
        $seekerData = [
            ['name' => 'Alice Cooper', 'email' => 'seeker@jobboard.com', 'skills' => ['PHP', 'Laravel', 'JavaScript', 'Vue.js']],
            ['name' => 'Bob Martinez', 'email' => 'bob@example.com', 'skills' => ['Python', 'Django', 'React', 'PostgreSQL']],
            ['name' => 'Carol White', 'email' => 'carol@example.com', 'skills' => ['UI/UX Design', 'Figma', 'Adobe XD', 'CSS']],
            ['name' => 'David Lee', 'email' => 'david@example.com', 'skills' => ['Java', 'Spring Boot', 'AWS', 'Docker']],
            ['name' => 'Eva Green', 'email' => 'eva@example.com', 'skills' => ['Digital Marketing', 'SEO', 'Google Analytics', 'Content Strategy']],
        ];

        $seekers = [];
        foreach ($seekerData as $index => $seeker) {
            
            
            $hasResume = $index < count($seekerData) - 1;

            $seekers[] = User::create([
                'name' => $seeker['name'],
                'email' => $seeker['email'],
                'password' => Hash::make('password'),
                'role' => 'seeker',
                'status' => 'active',
                'email_verified_at' => now(),
                'skills' => $seeker['skills'],
                'bio' => 'Experienced professional looking for new opportunities.',
                'location' => 'Casablanca',
                'availability' => 'available',
                'resume_path' => $hasResume ? 'resumes/sample-resume.pdf' : null,
                'resume_file_name' => $hasResume ? Str::slug($seeker['name']) . '-resume.pdf' : null,
                'resume_uploaded_at' => $hasResume ? now()->subDays(rand(5, 40)) : null,
            ]);
        }

        
        
        
        
        
        $jobs = [
            ['title' => 'Senior Laravel Developer', 'company' => 0, 'category' => 1, 'type' => 'full-time', 'exp' => 'senior', 'edu' => 'bac+5', 'location' => 'Casablanca', 'salary_min' => 18000, 'salary_max' => 28000, 'skills' => ['PHP', 'Laravel', 'MySQL', 'Redis', 'Vue.js']],
            ['title' => 'Frontend React Developer', 'company' => 0, 'category' => 1, 'type' => 'remote', 'exp' => 'mid_level', 'edu' => 'bac+3', 'location' => 'Casablanca', 'salary_min' => 14000, 'salary_max' => 22000, 'skills' => ['JavaScript', 'React', 'TypeScript', 'CSS']],
            ['title' => 'UI/UX Designer', 'company' => 1, 'category' => 2, 'type' => 'full-time', 'exp' => 'mid_level', 'edu' => 'bac+3', 'location' => 'Rabat', 'salary_min' => 12000, 'salary_max' => 20000, 'skills' => ['Figma', 'Adobe XD', 'Prototyping', 'User Research']],
            ['title' => 'Graphic Designer', 'company' => 1, 'category' => 2, 'type' => 'part-time', 'exp' => 'entry_level', 'edu' => 'bac+2', 'location' => 'Rabat', 'salary_min' => 6000, 'salary_max' => 10000, 'skills' => ['Photoshop', 'Illustrator', 'InDesign']],
            ['title' => 'DevOps Engineer', 'company' => 2, 'category' => 1, 'type' => 'remote', 'exp' => 'senior', 'edu' => 'bac+5', 'location' => 'Marrakech', 'salary_min' => 20000, 'salary_max' => 32000, 'skills' => ['AWS', 'Docker', 'Kubernetes', 'Terraform', 'CI/CD']],
            ['title' => 'Cloud Architect', 'company' => 2, 'category' => 1, 'type' => 'full-time', 'exp' => 'lead', 'edu' => 'bac+5', 'location' => 'Marrakech', 'salary_min' => 25000, 'salary_max' => 40000, 'skills' => ['AWS', 'Azure', 'Architecture', 'Security']],
            ['title' => 'Registered Nurse', 'company' => 3, 'category' => 6, 'type' => 'full-time', 'exp' => 'mid_level', 'edu' => 'bac+3', 'location' => 'Tangier', 'salary_min' => 9000, 'salary_max' => 14000, 'skills' => ['Patient Care', 'EMR', 'Critical Thinking']],
            ['title' => 'Financial Analyst', 'company' => 4, 'category' => 5, 'type' => 'full-time', 'exp' => 'entry_level', 'edu' => 'bac+3', 'location' => 'Fes', 'salary_min' => 10000, 'salary_max' => 16000, 'skills' => ['Excel', 'Financial Modeling', 'SQL', 'Python']],
            ['title' => 'Digital Marketing Manager', 'company' => 0, 'category' => 3, 'type' => 'full-time', 'exp' => 'mid_level', 'edu' => 'bac+3', 'location' => 'Casablanca', 'salary_min' => 12000, 'salary_max' => 18000, 'skills' => ['SEO', 'Google Ads', 'Content Marketing', 'Analytics']],
            ['title' => 'Junior Python Developer', 'company' => 2, 'category' => 1, 'type' => 'remote', 'exp' => 'entry_level', 'edu' => 'bac+2', 'location' => 'Marrakech', 'salary_min' => 8000, 'salary_max' => 13000, 'skills' => ['Python', 'Django', 'SQL', 'Git']],
            ['title' => 'Sales Representative', 'company' => 4, 'category' => 4, 'type' => 'full-time', 'exp' => 'entry_level', 'edu' => 'bac', 'location' => 'Fes', 'salary_min' => 6000, 'salary_max' => 11000, 'skills' => ['CRM', 'Communication', 'Negotiation']],
            ['title' => 'Part-time Customer Support', 'company' => 0, 'category' => 9, 'type' => 'part-time', 'exp' => 'entry_level', 'edu' => 'none', 'location' => 'Casablanca', 'salary_min' => 4000, 'salary_max' => 7000, 'skills' => ['Communication', 'Problem Solving', 'Zendesk']],
            ['title' => 'HR Coordinator', 'company' => 2, 'category' => 10, 'type' => 'full-time', 'exp' => 'mid_level', 'edu' => 'bac+3', 'location' => 'Marrakech', 'salary_min' => 9000, 'salary_max' => 14000, 'skills' => ['HRIS', 'Onboarding', 'Benefits Administration']],
            ['title' => 'Content Writer', 'company' => 1, 'category' => 3, 'type' => 'remote', 'exp' => 'mid_level', 'edu' => 'bac+2', 'location' => 'Rabat', 'salary_min' => 7000, 'salary_max' => 12000, 'skills' => ['Copywriting', 'SEO Writing', 'Research']],
            ['title' => 'Data Scientist Intern', 'company' => 0, 'category' => 1, 'type' => 'internship', 'exp' => 'entry_level', 'edu' => 'bac+3', 'location' => 'Casablanca', 'salary_min' => 4000, 'salary_max' => 6000, 'skills' => ['Python', 'Machine Learning', 'Statistics', 'Pandas']],
        ];

        $jobModels = [];
        foreach ($jobs as $i => $job) {
            $employer = $employerModels[$job['company']];
            
            $location = $job['type'] === 'remote' ? null : $job['location'];

            $jobModels[] = JobListing::create([
                'user_id' => $employer->id,
                'category_id' => $job['category'],
                'title' => $job['title'],
                'slug' => Str::slug($job['title']) . '-' . ($i + 1),
                'description' => "<p>We are looking for a talented <strong>{$job['title']}</strong> to join our team at {$employer->company_name}.</p><p>In this role, you will work with our team to build and ship amazing products. We value creativity, collaboration, and continuous learning.</p>",
                'requirements' => '<ul><li>Relevant experience in the field</li><li>Strong communication skills</li><li>Team player with a growth mindset</li></ul>',
                'benefits' => '<ul><li>Competitive salary</li><li>Health insurance</li><li>Flexible work schedule</li><li>Professional development budget</li></ul>',
                'type' => $job['type'],
                'experience_level' => $job['exp'],
                'education_level' => $job['edu'],
                'location' => $location,
                'salary_min' => $job['salary_min'],
                'salary_max' => $job['salary_max'],
                'skills' => $job['skills'],
                'status' => 'active',   
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        
        
        $acceptMessages = [
            "Congratulations! We'd like to invite you for an interview. Are you available next week?",
            "Great news, we're excited to move forward with your application.",
            "We were impressed by your profile. Let's schedule a call to discuss next steps.",
        ];
        $rejectMessages = [
            "Thank you for applying. We've decided to move forward with other candidates.",
            "We appreciate your interest, but won't be proceeding at this time. We'll keep your resume on file.",
        ];

        foreach ($seekers as $seeker) {
            $randomJobs = collect($jobModels)->shuffle()->take(rand(2, 5));
            foreach ($randomJobs as $job) {
                $status = collect(['pending', 'accepted', 'rejected'])->random();
                $responseMessage = match ($status) {
                    'accepted' => $acceptMessages[array_rand($acceptMessages)],
                    'rejected' => $rejectMessages[array_rand($rejectMessages)],
                    default => null,
                };

                Application::create([
                    'user_id' => $seeker->id,
                    'job_listing_id' => $job->id,
                    
                    'resume_path' => $seeker->resume_path,
                    'resume_file_name' => $seeker->resume_file_name,
                    'cv_is_default' => true,
                    'status' => $status,
                    'response_message' => $responseMessage,
                    'responded_at' => $status === 'pending' ? null : now()->subDays(rand(0, 5)),
                ]);
            }
        }

        
        foreach ($seekers as $seeker) {
            $randomJobs = collect($jobModels)->shuffle()->take(rand(1, 4));
            foreach ($randomJobs as $job) {
                SavedJob::firstOrCreate([
                    'user_id' => $seeker->id,
                    'job_listing_id' => $job->id,
                ]);
            }
        }
    }
}
