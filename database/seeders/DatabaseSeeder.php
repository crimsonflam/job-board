<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobAlert;
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
        // Categories
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'icon' => '💻'],
            ['name' => 'Design', 'slug' => 'design', 'icon' => '🎨'],
            ['name' => 'Marketing', 'slug' => 'marketing', 'icon' => '📢'],
            ['name' => 'Sales', 'slug' => 'sales', 'icon' => '💰'],
            ['name' => 'Finance', 'slug' => 'finance', 'icon' => '📊'],
            ['name' => 'Healthcare', 'slug' => 'healthcare', 'icon' => '🏥'],
            ['name' => 'Education', 'slug' => 'education', 'icon' => '📚'],
            ['name' => 'Engineering', 'slug' => 'engineering', 'icon' => '⚙️'],
            ['name' => 'Customer Service', 'slug' => 'customer-service', 'icon' => '🎧'],
            ['name' => 'Human Resources', 'slug' => 'human-resources', 'icon' => '👥'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@jobboard.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Employer users and companies
        $employers = [
            ['name' => 'John Smith', 'email' => 'employer@jobboard.com', 'company' => 'TechCorp Solutions', 'industry' => 'Technology', 'size' => '201-500', 'verified' => true],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@designhub.com', 'company' => 'DesignHub Agency', 'industry' => 'Design', 'size' => '11-50', 'verified' => true],
            ['name' => 'Mike Williams', 'email' => 'mike@cloudnine.com', 'company' => 'CloudNine Inc', 'industry' => 'Technology', 'size' => '51-200', 'verified' => true],
            ['name' => 'Emily Davis', 'email' => 'emily@healthplus.com', 'company' => 'HealthPlus Medical', 'industry' => 'Healthcare', 'size' => '500+', 'verified' => false],
            ['name' => 'Robert Brown', 'email' => 'robert@finserv.com', 'company' => 'FinServ Group', 'industry' => 'Finance', 'size' => '51-200', 'verified' => true],
        ];

        $companyModels = [];
        foreach ($employers as $emp) {
            $user = User::create([
                'name' => $emp['name'],
                'email' => $emp['email'],
                'password' => Hash::make('password'),
                'role' => 'employer',
                'email_verified_at' => now(),
            ]);

            $locations = ['New York, NY', 'San Francisco, CA', 'Austin, TX', 'Boston, MA', 'Chicago, IL'];
            $companyModels[] = Company::create([
                'user_id' => $user->id,
                'name' => $emp['company'],
                'slug' => Str::slug($emp['company']),
                'description' => "We are {$emp['company']}, a leading company in the {$emp['industry']} industry. We offer competitive salaries, great benefits, and a fantastic work culture.",
                'industry' => $emp['industry'],
                'size' => $emp['size'],
                'location' => $locations[array_rand($locations)],
                'website' => 'https://' . Str::slug($emp['company']) . '.com',
                'email' => 'contact@' . Str::slug($emp['company']) . '.com',
                'is_verified' => $emp['verified'],
            ]);
        }

        // Job seeker users
        $seekers = [];
        $seekerData = [
            ['name' => 'Alice Cooper', 'email' => 'seeker@jobboard.com', 'skills' => ['PHP', 'Laravel', 'JavaScript', 'Vue.js']],
            ['name' => 'Bob Martinez', 'email' => 'bob@example.com', 'skills' => ['Python', 'Django', 'React', 'PostgreSQL']],
            ['name' => 'Carol White', 'email' => 'carol@example.com', 'skills' => ['UI/UX Design', 'Figma', 'Adobe XD', 'CSS']],
            ['name' => 'David Lee', 'email' => 'david@example.com', 'skills' => ['Java', 'Spring Boot', 'AWS', 'Docker']],
            ['name' => 'Eva Green', 'email' => 'eva@example.com', 'skills' => ['Digital Marketing', 'SEO', 'Google Analytics', 'Content Strategy']],
        ];

        foreach ($seekerData as $seeker) {
            $seekers[] = User::create([
                'name' => $seeker['name'],
                'email' => $seeker['email'],
                'password' => Hash::make('password'),
                'role' => 'seeker',
                'email_verified_at' => now(),
                'skills' => $seeker['skills'],
                'bio' => "Experienced professional looking for new opportunities.",
                'location' => 'United States',
                'availability' => 'available',
            ]);
        }

        // Job listings
        $jobs = [
            ['title' => 'Senior Laravel Developer', 'company' => 0, 'category' => 1, 'type' => 'full-time', 'exp' => 'senior', 'location' => 'New York, NY', 'remote' => true, 'salary_min' => 120000, 'salary_max' => 160000, 'featured' => true, 'skills' => ['PHP', 'Laravel', 'MySQL', 'Redis', 'Vue.js']],
            ['title' => 'Frontend React Developer', 'company' => 0, 'category' => 1, 'type' => 'full-time', 'exp' => 'mid', 'location' => 'New York, NY', 'remote' => true, 'salary_min' => 90000, 'salary_max' => 130000, 'featured' => false, 'skills' => ['JavaScript', 'React', 'TypeScript', 'CSS']],
            ['title' => 'UI/UX Designer', 'company' => 1, 'category' => 2, 'type' => 'full-time', 'exp' => 'mid', 'location' => 'San Francisco, CA', 'remote' => false, 'salary_min' => 85000, 'salary_max' => 120000, 'featured' => true, 'skills' => ['Figma', 'Adobe XD', 'Prototyping', 'User Research']],
            ['title' => 'Graphic Designer', 'company' => 1, 'category' => 2, 'type' => 'contract', 'exp' => 'entry', 'location' => 'Remote', 'remote' => true, 'salary_min' => 50000, 'salary_max' => 70000, 'featured' => false, 'skills' => ['Photoshop', 'Illustrator', 'InDesign']],
            ['title' => 'DevOps Engineer', 'company' => 2, 'category' => 1, 'type' => 'full-time', 'exp' => 'senior', 'location' => 'Austin, TX', 'remote' => true, 'salary_min' => 130000, 'salary_max' => 170000, 'featured' => true, 'skills' => ['AWS', 'Docker', 'Kubernetes', 'Terraform', 'CI/CD']],
            ['title' => 'Cloud Architect', 'company' => 2, 'category' => 1, 'type' => 'full-time', 'exp' => 'lead', 'location' => 'Austin, TX', 'remote' => false, 'salary_min' => 150000, 'salary_max' => 200000, 'featured' => false, 'skills' => ['AWS', 'Azure', 'Architecture', 'Security']],
            ['title' => 'Registered Nurse', 'company' => 3, 'category' => 6, 'type' => 'full-time', 'exp' => 'mid', 'location' => 'Boston, MA', 'remote' => false, 'salary_min' => 65000, 'salary_max' => 85000, 'featured' => false, 'skills' => ['Patient Care', 'EMR', 'Critical Thinking']],
            ['title' => 'Financial Analyst', 'company' => 4, 'category' => 5, 'type' => 'full-time', 'exp' => 'entry', 'location' => 'Chicago, IL', 'remote' => false, 'salary_min' => 60000, 'salary_max' => 80000, 'featured' => true, 'skills' => ['Excel', 'Financial Modeling', 'SQL', 'Python']],
            ['title' => 'Digital Marketing Manager', 'company' => 0, 'category' => 3, 'type' => 'full-time', 'exp' => 'mid', 'location' => 'New York, NY', 'remote' => true, 'salary_min' => 75000, 'salary_max' => 100000, 'featured' => false, 'skills' => ['SEO', 'Google Ads', 'Content Marketing', 'Analytics']],
            ['title' => 'Junior Python Developer', 'company' => 2, 'category' => 1, 'type' => 'full-time', 'exp' => 'entry', 'location' => 'Austin, TX', 'remote' => true, 'salary_min' => 60000, 'salary_max' => 80000, 'featured' => false, 'skills' => ['Python', 'Django', 'SQL', 'Git']],
            ['title' => 'Sales Representative', 'company' => 4, 'category' => 4, 'type' => 'full-time', 'exp' => 'entry', 'location' => 'Chicago, IL', 'remote' => false, 'salary_min' => 45000, 'salary_max' => 65000, 'featured' => false, 'skills' => ['CRM', 'Communication', 'Negotiation']],
            ['title' => 'Part-time Customer Support', 'company' => 0, 'category' => 9, 'type' => 'part-time', 'exp' => 'entry', 'location' => 'Remote', 'remote' => true, 'salary_min' => 25000, 'salary_max' => 35000, 'featured' => false, 'skills' => ['Communication', 'Problem Solving', 'Zendesk']],
            ['title' => 'HR Coordinator', 'company' => 2, 'category' => 10, 'type' => 'full-time', 'exp' => 'mid', 'location' => 'Austin, TX', 'remote' => false, 'salary_min' => 55000, 'salary_max' => 70000, 'featured' => false, 'skills' => ['HRIS', 'Onboarding', 'Benefits Administration']],
            ['title' => 'Freelance Content Writer', 'company' => 1, 'category' => 3, 'type' => 'freelance', 'exp' => 'mid', 'location' => 'Remote', 'remote' => true, 'salary_min' => 40000, 'salary_max' => 60000, 'featured' => false, 'skills' => ['Copywriting', 'SEO Writing', 'Research']],
            ['title' => 'Data Scientist Intern', 'company' => 0, 'category' => 1, 'type' => 'internship', 'exp' => 'entry', 'location' => 'New York, NY', 'remote' => false, 'salary_min' => 30000, 'salary_max' => 40000, 'featured' => false, 'skills' => ['Python', 'Machine Learning', 'Statistics', 'Pandas']],
        ];

        $jobModels = [];
        foreach ($jobs as $i => $job) {
            $company = $companyModels[$job['company']];
            $jobModels[] = JobListing::create([
                'company_id' => $company->id,
                'user_id' => $company->user_id,
                'category_id' => $job['category'],
                'title' => $job['title'],
                'slug' => Str::slug($job['title']) . '-' . ($i + 1),
                'description' => "<p>We are looking for a talented <strong>{$job['title']}</strong> to join our team at {$company->name}.</p><p>In this role, you will work with our team to build and ship amazing products. We value creativity, collaboration, and continuous learning.</p><p>This is an exciting opportunity to make a real impact in a growing company.</p>",
                'requirements' => "<ul><li>Relevant experience in the field</li><li>Strong communication skills</li><li>Team player with a growth mindset</li><li>Proficiency in required skills</li></ul>",
                'benefits' => "<ul><li>Competitive salary and equity</li><li>Health, dental, and vision insurance</li><li>Flexible work schedule</li><li>Professional development budget</li><li>Annual team retreats</li></ul>",
                'type' => $job['type'],
                'experience_level' => $job['exp'],
                'location' => $job['location'],
                'is_remote' => $job['remote'],
                'salary_min' => $job['salary_min'],
                'salary_max' => $job['salary_max'],
                'salary_currency' => 'USD',
                'skills' => $job['skills'],
                'status' => 'published',
                'is_featured' => $job['featured'],
                'published_at' => now()->subDays(rand(1, 30)),
                'expires_at' => now()->addDays(rand(10, 60)),
                'views_count' => rand(10, 500),
            ]);
        }

        // Applications
        $statuses = ['pending', 'reviewed', 'shortlisted', 'rejected', 'hired'];
        foreach ($seekers as $seeker) {
            $randomJobs = collect($jobModels)->shuffle()->take(rand(2, 5));
            foreach ($randomJobs as $job) {
                Application::create([
                    'user_id' => $seeker->id,
                    'job_listing_id' => $job->id,
                    'cover_letter' => "Dear Hiring Manager,\n\nI am excited to apply for the {$job->title} position at your company. With my background and skills, I believe I would be a great fit for this role.\n\nThank you for considering my application.\n\nBest regards,\n{$seeker->name}",
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }

        // Saved jobs
        foreach ($seekers as $seeker) {
            $randomJobs = collect($jobModels)->shuffle()->take(rand(1, 4));
            foreach ($randomJobs as $job) {
                SavedJob::firstOrCreate([
                    'user_id' => $seeker->id,
                    'job_listing_id' => $job->id,
                ]);
            }
        }

        // Job alerts for first seeker
        JobAlert::create([
            'user_id' => $seekers[0]->id,
            'keyword' => 'Laravel',
            'category_id' => 1,
            'frequency' => 'daily',
            'is_active' => true,
        ]);

        JobAlert::create([
            'user_id' => $seekers[0]->id,
            'keyword' => 'Remote',
            'is_remote' => true,
            'frequency' => 'weekly',
            'is_active' => true,
        ]);
    }
}
