# Job Scraper Cron Setup Guide

## Overview
The job scraper automatically fetches external job postings from configured sources and imports them into your database.

## Command Details

### Artisan Command
```bash
php artisan jobs:scrape
```

### Command Options
- `php artisan jobs:scrape` - Scrape all active job feed sources
- `php artisan jobs:scrape {source-slug}` - Scrape a specific source
- `php artisan jobs:scrape --dry-run` - Test without importing

### Files
- **Command**: `app/Console/Commands/ScrapeJobFeeds.php`
- **Schedule**: `app/Console/Kernel.php`
- **Model**: `app/JobFeedSource.php`
- **Runs Model**: `app/JobFeedRun.php`

## Current Schedule Configuration

The scraper is now scheduled to run **every hour** with the following settings:

```php
$schedule->command('jobs:scrape')
    ->hourly()                          // Runs every hour
    ->withoutOverlapping(10)            // Prevents overlapping runs (10 min timeout)
    ->sendOutputTo(storage_path() . '/logs/job-scraper.log');  // Logs output
```

## Server Cron Job Setup

### Step 1: Add Laravel Scheduler to Crontab

On your server, you need to add ONE cron entry that runs Laravel's scheduler every minute:

```bash
# Edit crontab
crontab -e

# Add this line (replace /path/to/your/project with actual path)
* * * * * cd /var/www/html/carejobber && php artisan schedule:run >> /dev/null 2>&1
```

### Step 2: Verify Cron is Running

```bash
# Check if cron service is running
sudo systemctl status cron

# View crontab entries
crontab -l

# Monitor the log file
tail -f storage/logs/job-scraper.log
```

## Schedule Options

You can change the frequency by modifying `app/Console/Kernel.php`:

### Every Hour (Current)
```php
$schedule->command('jobs:scrape')->hourly();
```

### Every 30 Minutes
```php
$schedule->command('jobs:scrape')->everyThirtyMinutes();
```

### Every 2 Hours
```php
$schedule->command('jobs:scrape')->everyTwoHours();
```

### Every 6 Hours
```php
$schedule->command('jobs:scrape')->everySixHours();
```

### Twice Daily (8am and 8pm)
```php
$schedule->command('jobs:scrape')->twiceDaily(8, 20);
```

### Daily at Specific Time
```php
$schedule->command('jobs:scrape')->dailyAt('02:00');
```

## Testing the Scraper

### Test Manually
```bash
# Run the scraper manually
php artisan jobs:scrape

# Test without importing
php artisan jobs:scrape --dry-run

# Test specific source
php artisan jobs:scrape indeed
```

### Check Logs
```bash
# View scraper log
tail -f storage/logs/job-scraper.log

# View all scheduled tasks
php artisan schedule:list
```

## Database Tables

### job_feed_sources
Stores configured external job sources:
- `id` - Source ID
- `name` - Source name
- `slug` - Source identifier
- `provider` - Provider type (indeed, linkedin, etc.)
- `is_active` - Whether source is active
- `last_run_at` - Last scrape timestamp

### job_feed_runs
Tracks each scraper execution:
- `id` - Run ID
- `job_feed_source_id` - Source being scraped
- `status` - running, completed, failed, skipped
- `discovered_count` - Jobs found
- `imported_count` - Jobs imported
- `updated_count` - Jobs updated
- `skipped_count` - Jobs skipped
- `error_message` - Error details if failed
- `started_at` - Start time
- `finished_at` - End time

### medo_scraper_runs
Alternative scraper runs table for Medo-specific scrapers.

## Troubleshooting

### Scraper Not Running
1. Check if cron is active: `sudo systemctl status cron`
2. Verify crontab entry: `crontab -l`
3. Check Laravel scheduler: `php artisan schedule:list`
4. Run manually to test: `php artisan jobs:scrape`

### No Jobs Being Imported
1. Check if sources are active: `SELECT * FROM job_feed_sources WHERE is_active = 1`
2. Check scraper runs: `SELECT * FROM job_feed_runs ORDER BY id DESC LIMIT 10`
3. Review error messages in runs table
4. Check log file: `storage/logs/job-scraper.log`

### Permission Issues
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

## Current Scheduled Tasks

All scheduled tasks in your application:

1. **Queue Worker** - Every 5 minutes
2. **Package Validity Check** - Daily
3. **Send Alerts** - Daily
4. **Incomplete Profile Reminders** - Weekly
5. **Job Scraper** - Every hour (NEW)

## Notes

- The scraper uses `withoutOverlapping(10)` to prevent concurrent runs
- Logs are stored in `storage/logs/job-scraper.log`
- Each run is recorded in the `job_feed_runs` table
- Only active sources (`is_active = 1`) are scraped
- The scraper currently shows "No scraper adapter registered" - you need to implement the actual scraper logic for each provider
