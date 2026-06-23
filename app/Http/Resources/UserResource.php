<?php
//send data to front
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,

            'is_seeker' => $this->isSeeker(),
            'is_employer' => $this->isEmployer(),
            'is_admin' => $this->isAdmin(),
            'is_super_admin' => $this->isSuperAdmin(),
            'is_active' => $this->isActive(),

            'phone' => $this->phone,
            'bio' => $this->bio,
            'location' => $this->location,
            'website' => $this->website,

            'resume_file_name' => $this->resume_file_name,
            'resume_uploaded_at' => $this->resume_uploaded_at,
            'has_default_resume' => $this->hasDefaultResume(),

            'company_name' => $this->company_name,
            'company_description' => $this->company_description,
            'company_location' => $this->company_location,
            'company_website' => $this->company_website,
            'industry' => $this->industry,
            'company_display_name' => $this->companyDisplayName(),
            'has_company_profile' => $this->hasCompanyProfile(),

            'can_manage' => $this->when(
                $request->user() && $request->user()->isAdmin(),
                fn () => $request->user()->canManage($this->resource)
            ),

            'created_at' => $this->created_at,
        ];
    }
}
