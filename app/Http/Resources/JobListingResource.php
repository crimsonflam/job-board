<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class JobListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isSeeker = $user && $user->isSeeker();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'type' => $this->type,
            'experience_level' => $this->experience_level,
            'education_level' => $this->education_level,
            'location' => $this->location,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'skills' => $this->skills,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'type_label' => $this->typeLabel(),
            'education_label' => $this->educationLabel(),
            'experience_label' => $this->experienceLabel(),
            'salary_range' => $this->salaryRange(),
            'is_remote' => $this->isRemote(),
            'company' => $this->whenLoaded('user', fn () => [
                'name' => $this->user->companyDisplayName(),
                'company_name' => $this->user->company_name,
                'industry' => $this->user->industry,
                'company_description' => $this->user->company_description,
                'company_location' => $this->user->company_location,
                'company_website' => $this->user->company_website,
            ]),

            'category' => new CategoryResource($this->whenLoaded('category')),
            'applications_count' => $this->whenCounted('applications'),
            'is_saved' => $this->when($isSeeker, fn () => $user->hasSavedJob($this->id)),
            'has_applied' => $this->when($isSeeker, fn () => $user->hasApplied($this->id)),
        ];
    }
}
