# Quick Fix Commands for Production Server

## Option 1: Using Artisan Command (Recommended)

After deploying the code changes, run this single command:

```bash
php artisan sync:job-categories
```

This command will:
- Sync all missing job categories from functional_areas
- Update all jobs with job_category_id
- Show you what was added

---

## Option 2: Using SQL Queries Directly

If you prefer to run SQL queries directly, execute these in order:

### 1. Sync Job Categories
```sql
INSERT INTO job_categories (job_category_id, job_category, is_default, image, is_active, sort_order, lang, slug, created_at, updated_at)
SELECT fa.functional_area_id, fa.functional_area, fa.is_default, fa.image, fa.is_active, fa.sort_order, fa.lang, fa.slug, fa.created_at, fa.updated_at
FROM functional_areas fa
WHERE fa.functional_area_id NOT IN (SELECT job_category_id FROM job_categories WHERE job_category_id IS NOT NULL);
```

### 2. Update Jobs Table
```sql
UPDATE jobs SET job_category_id = functional_area_id WHERE functional_area_id IS NOT NULL AND (job_category_id IS NULL OR job_category_id = 0);
```

### 3. Update Users Table
```sql
UPDATE users SET job_category_id = functional_area_id WHERE functional_area_id IS NOT NULL AND (job_category_id IS NULL OR job_category_id = 0);
```

---

## After Running Either Option

Clear the application cache:

```bash
php artisan cache:clear
php artisan view:clear
```

---

## Files Changed (Deploy These)

1. **app/Job.php** - Added automatic sync of job_category_id from functional_area_id
2. **app/Console/Commands/SyncJobCategories.php** - New command for syncing (optional but recommended)

---

## Verification

Check if the fix worked:

```bash
php artisan tinker --execute="echo 'Active Jobs with job_category_id: ' . App\Job::where('is_active', 1)->whereNotNull('job_category_id')->notExpire()->count() . PHP_EOL;"
```

Or visit: `http://your-domain.com/jobs` and check if "Jobs By Category" section shows categories.

---

## For Cron Setup

If you want to ensure this stays synced automatically, add this to your crontab:

```bash
# Sync job categories daily at 2 AM
0 2 * * * cd /path/to/your/project && php artisan sync:job-categories >> /dev/null 2>&1
```

This is optional but recommended to catch any edge cases where new functional areas are added.
