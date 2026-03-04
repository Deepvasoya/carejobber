<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class GeneratePartialResumeData extends Command
{
    protected $signature = 'resume:generate-partial-data {--force : Regenerate even if partial_data exists}';
    protected $description = 'Generate partial_data JSON for all users (work history + education)';

    public function handle()
    {
        $force = $this->option('force');
        $query = User::query();

        if (!$force) {
            $query->whereNull('partial_data');
        }

        $users = $query->get();
        $this->info("Processing {$users->count()} users...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $history = $user->profileExperience()->get()->map(function ($exp) {
                return [
                    'title' => $exp->title ?? '',
                    'company' => $exp->company ?? '',
                    'start_date' => $exp->date_start ?? '',
                    'end_date' => $exp->date_end ?? '',
                    'description' => $exp->description ?? '',
                ];
            })->toArray();

            $education = $user->profileEducation()->get()->map(function ($edu) {
                return [
                    'degree' => $edu->degree_level_id ?? '',
                    'institution' => $edu->institute ?? '',
                    'start_date' => $edu->date_start ?? '',
                    'end_date' => $edu->date_end ?? '',
                ];
            })->toArray();

            $user->partial_data = [
                'work_history' => $history,
                'education' => $education,
            ];
            $user->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done! Generated partial_data for ' . $users->count() . ' users.');
    }
}
