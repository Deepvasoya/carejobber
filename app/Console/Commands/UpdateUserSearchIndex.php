<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Traits\CommonUserFunctions;

class UpdateUserSearchIndex extends Command
{
    use CommonUserFunctions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-search-index {--user_id= : Update specific user only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the full-text search index for all users or a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user_id');

        if ($userId) {
            // Update specific user
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
            $this->info("Updating search index for user: {$user->getName()}");
            $this->updateUserFullTextSearch($user);
            $this->info("Search index updated successfully for user ID: {$userId}");
        } else {
            // Update all users
            $this->info('Starting to update search index for all users...');
            $users = User::where('is_active', 1)->get();
            $total = $users->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($users as $user) {
                try {
                    $this->updateUserFullTextSearch($user);
                } catch (\Exception $e) {
                    $this->error("\nError updating user {$user->id}: " . $e->getMessage());
                }
                $bar->advance();
            }

            $bar->finish();
            $this->info("\n\nSearch index updated successfully for {$total} users.");
        }

        return 0;
    }
}
