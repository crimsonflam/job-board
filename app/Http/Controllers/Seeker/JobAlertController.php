<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\JobAlert;
use Illuminate\Http\Request;

class JobAlertController extends Controller
{
    public function index()
    {
        $alerts = auth()->user()->jobAlerts()->with('category')->latest()->get();
        $categories = Category::all();
        return view('seeker.alerts.index', compact('alerts', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'in:full-time,part-time,contract,freelance,internship'],
            'is_remote' => ['nullable', 'boolean'],
            'frequency' => ['required', 'in:daily,weekly'],
        ]);

        auth()->user()->jobAlerts()->create($validated);

        return back()->with('success', 'Job alert created!');
    }

    public function destroy(JobAlert $jobAlert)
    {
        if ($jobAlert->user_id !== auth()->id()) {
            abort(403);
        }

        $jobAlert->delete();
        return back()->with('success', 'Job alert deleted.');
    }
}
