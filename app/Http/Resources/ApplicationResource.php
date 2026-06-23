<?php
// this file represent what to send to the frontend
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'response_message' => $this->response_message,
            'responded_at' => $this->responded_at,
            'created_at' => $this->created_at,

            'resume_file_name' => $this->resume_file_name,
            'cv_is_default' => $this->cv_is_default,
            'has_resume' => filled($this->resume_path),
            'resume_url' => $this->when(
                filled($this->resume_path),
                fn () => Storage::disk('public')->url($this->resume_path)
            ),

            'status_label' => $this->statusLabel(),
            'status_badge_color' => $this->statusBadgeColor(),
            'has_response' => $this->hasResponse(),

            'job_listing' => new JobListingResource($this->whenLoaded('jobListing')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
