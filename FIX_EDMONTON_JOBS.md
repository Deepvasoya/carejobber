# Fix Edmonton Jobs - Step by Step Instructions

## Problem
The 4 Edmonton HCA jobs you posted through the employer interface are in the `jobs` table, but the pSEO page at `/jobs/hca/ab/edmonton` shows 0 jobs because it queries the `medo_jobs` table.

## Root Cause
The sync command fails with error: **"Column 'employer_id' cannot be null"** because the production database has a NOT NULL constraint on `medo_jobs.employer_id`.

## Solution - Run These Commands in Order

### Step 1: Run the Migration (CRITICAL - DO THIS FIRST)
```bash
php artisan migrate
```

This will make the `employer_id` column nullable in the `medo_jobs` table, allowing legacy jobs to be synced without an employer_id.

**Expected output:**
```
Migrating: 2026_05_08_074434_make_employer_id_nullable_in_medo_jobs_table
Migrated:  2026_05_08_074434_make_employer_id_nullable_in_medo_jobs_table
```

### Step 2: Diagnose Your Legacy Jobs (Optional but Recommended)
```bash
php artisan jobs:diagnose-legacy
```

This will show you:
- All active jobs in the legacy `jobs` table
- Which jobs are in Edmonton (city_id=10125)
- What functional_area_id each job has
- Distribution of functional areas

This helps verify that your 4 Edmonton HCA jobs exist and have the correct functional area.

### Step 3: Run the Sync Command
```bash
php artisan jobs:sync-legacy-to-medo
```

This will:
- Find all active jobs in the legacy `jobs` table
- Map them to medo categories based on functional area name (HCA, LPN, RN)
- Map them to medo cities (Calgary, Edmonton, Red Deer, etc.)
- Insert them into `medo_jobs` table with `employer_id = NULL`

**Expected output:**
```
Starting sync of legacy jobs to medo_jobs...
Found X eligible legacy jobs
Created: [Job Title]
Created: [Job Title]
...
Sync complete!
Synced: X
Skipped: X
Errors: 0
```

### Step 4: Clear Cache
```bash
php artisan cache:clear
```

The controller caches job listings for 1 hour. This clears the cache so you see fresh data immediately.

### Step 5: Verify
Visit: `http://your-domain.com/jobs/hca/ab/edmonton`

You should now see your 4 Edmonton HCA jobs listed.

## What Changed

### 1. Migration Created
- **File:** `database/migrations/2026_05_08_074434_make_employer_id_nullable_in_medo_jobs_table.php`
- **What it does:** Changes `employer_id` column from NOT NULL to nullable
- **Why:** Legacy jobs don't have medo employers, so employer_id must be nullable

### 2. Sync Command Improved
- **File:** `app/Console/Commands/SyncLegacyJobsToMedo.php`
- **Changes:**
  - Now maps by functional area **name** instead of hardcoded IDs
  - Supports variations: "Health Care Aide", "HCA", "Care Aide" all map to category 1
  - Shows detailed output when skipping jobs
  - Loads functionalArea relationship for better mapping

### 3. Diagnostic Command Added
- **File:** `app/Console/Commands/DiagnoseLegacyJobs.php`
- **What it does:** Shows all active jobs in legacy table with their IDs, cities, and functional areas
- **Why:** Helps troubleshoot mapping issues

### 4. Automatic Sync Scheduled
- **File:** `app/Console/Kernel.php`
- **What it does:** Runs sync command every 15 minutes automatically
- **Why:** New employer-posted jobs will appear on pSEO pages within 15 minutes

## Troubleshooting

### If sync still shows 0 jobs synced:
1. Run `php artisan jobs:diagnose-legacy` to see what's in your legacy jobs table
2. Check if the functional area names match the mapping in `buildCategoryMapping()`
3. Check if the city_id is in the city mapping (10125 = Edmonton)

### If jobs still don't appear on pSEO page:
1. Clear cache: `php artisan cache:clear`
2. Check the medo_jobs table directly: `SELECT * FROM medo_jobs WHERE city_id = 2`
3. Verify category_id=1 (HCA), province_id=1 (AB), city_id=2 (Edmonton)

### If you see "Skipping job X: No category mapping":
The functional area name doesn't match any mapping. Run the diagnose command to see the actual functional area name, then add it to `buildCategoryMapping()` in the sync command.

## Next Steps After This Works

Once jobs are syncing correctly:

1. **Set up cron job** for automatic scraping (see SCRAPER_CRON_SETUP.md)
2. **Implement real scrapers** for AHS, Covenant, Bethany, etc. (currently only demo scraper works)
3. **Test with more cities** - add more city mappings as needed
4. **Monitor sync errors** - check logs for any jobs that fail to sync

## Questions?

If you still see 0 jobs after following these steps, send me:
1. Output from `php artisan jobs:diagnose-legacy`
2. Output from `php artisan jobs:sync-legacy-to-medo`
3. Result of: `SELECT COUNT(*) FROM medo_jobs WHERE city_id = 2 AND category_id = 1`
