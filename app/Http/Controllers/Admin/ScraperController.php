<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\JobFeedRun;
use App\JobFeedSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ScraperController extends Controller
{
    public function index()
    {
        $sources = JobFeedSource::all();
        $runs = JobFeedRun::with('source')->orderByDesc('id')->take(50)->get();
        
        return view('admin.scraper.index', compact('sources', 'runs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'source_url' => 'required|url|max:500',
        ]);

        $slug = Str::slug($request->name);
        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (JobFeedSource::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        
        JobFeedSource::create([
            'name' => $request->name,
            'slug' => $slug,
            'provider' => 'sitemap',
            'source_url' => $request->source_url,
            'is_active' => true,
        ]);

        flash('Job feed source added successfully.')->success();
        return redirect()->back();
    }

    public function run()
    {
        set_time_limit(300); // 5 minutes max for synchronous execution
        
        try {
            Artisan::call('jobs:scrape');
            flash('Scraper completed successfully. Check the run logs below for details.')->success();
        } catch (\Exception $e) {
            flash('Scraper failed: ' . $e->getMessage())->error();
        }

        return redirect()->back();
    }
}
