<?php
// this file is for specifying the meta data for frontend
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\JobListing;

class MetaController extends Controller
{
    public function index()
    {
        return response()->json([
            'categories' => CategoryResource::collection(Category::orderBy('name')->get()),
            'cities' => config('morocco.cities'),
            'job_types' => JobListing::TYPE_LABELS,
            'experience_levels' => JobListing::EXPERIENCE_LABELS,
            'education_levels' => JobListing::EDUCATION_LABELS,
        ]);
    }
}
