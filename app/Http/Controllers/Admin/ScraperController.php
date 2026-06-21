<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\JobFeedRun;
use App\JobFeedSource;
use App\SchedulerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ScraperController extends Controller
{
    public function index()
    {
        $sources = JobFeedSource::all();
        $runs = JobFeedRun::with('source')->orderByDesc('id')->take(50)->get();
        $cronLogs = SchedulerLog::orderByDesc('started_at')->take(20)->get();
        
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $crons = [];
        foreach ($schedule->events() as $event) {
            $command = str_replace(["'/usr/bin/php8.4'", "'artisan'", 'artisan', '/usr/bin/php', '/usr/local/bin/php', 'php'], '', $event->command);
            $command = trim(preg_replace('/\s+/', ' ', $command));
            
            // Format next run date
            try {
                $nextRun = $event->nextRunDate()->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $nextRun = 'Unknown';
            }
            
            $crons[] = [
                'command' => $command ?: 'Closure / Callback',
                'expression' => $event->expression,
                'description' => $event->description,
                'next_run' => $nextRun,
            ];
        }
        
        return view('admin.scraper.index', compact('sources', 'runs', 'crons', 'cronLogs'));
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

    public function runCommand(Request $request)
    {
        $command = $request->get('command');
        $validCommands = [
            'jobs:scrape',
            'jobs:scrape ahs',
            'jobs:scrape covenant',
            'jobs:scrape ab-ltc',
            'jobs:scrape ab-agencies',
            'jobs:expire',
            'indexnow:ping'
        ];

        if (!in_array($command, $validCommands)) {
            flash('Invalid command requested.')->error();
            return redirect()->back();
        }

        set_time_limit(300);
        
        try {
            // Parse arguments if any
            $parts = explode(' ', $command);
            $baseCommand = $parts[0];
            $args = [];
            if (isset($parts[1])) {
                $args['source'] = $parts[1];
            }

            Artisan::call($baseCommand, $args);
            flash("Command '{$command}' completed successfully.")->success();
        } catch (\Exception $e) {
            flash("Command '{$command}' failed: " . $e->getMessage())->error();
        }

        return redirect()->back();
    }
}
