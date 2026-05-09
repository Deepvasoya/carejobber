-- ============================================================================
-- SQL QUERIES TO FIX "JOBS BY CATEGORY" ISSUE ON PRODUCTION SERVER
-- ============================================================================
-- Run these queries in order on your production database
-- ============================================================================

-- STEP 1: Sync missing job categories from functional_areas table
-- This will copy any functional areas that don't exist in job_categories
-- ============================================================================
INSERT INTO job_categories (
    job_category_id, 
    job_category, 
    is_default, 
    image, 
    is_active, 
    sort_order, 
    lang, 
    slug, 
    created_at, 
    updated_at
)
SELECT 
    fa.functional_area_id,
    fa.functional_area,
    fa.is_default,
    fa.image,
    fa.is_active,
    fa.sort_order,
    fa.lang,
    fa.slug,
    fa.created_at,
    fa.updated_at
FROM functional_areas fa
WHERE fa.functional_area_id NOT IN (
    SELECT job_category_id 
    FROM job_categories 
    WHERE job_category_id IS NOT NULL
);

-- ============================================================================
-- STEP 2: Update all jobs to have job_category_id from functional_area_id
-- This ensures all existing jobs have the job_category_id field populated
-- ============================================================================
UPDATE jobs 
SET job_category_id = functional_area_id 
WHERE functional_area_id IS NOT NULL 
  AND (job_category_id IS NULL OR job_category_id = 0);

-- ============================================================================
-- STEP 3: Update all users to have job_category_id from functional_area_id
-- This ensures user profiles also have the job_category_id field populated
-- ============================================================================
UPDATE users 
SET job_category_id = functional_area_id 
WHERE functional_area_id IS NOT NULL 
  AND (job_category_id IS NULL OR job_category_id = 0);

-- ============================================================================
-- VERIFICATION QUERIES (Optional - Run these to verify the fix worked)
-- ============================================================================

-- Check how many job categories exist
SELECT COUNT(*) as total_job_categories FROM job_categories;

-- Check how many active jobs have job_category_id set
SELECT COUNT(*) as jobs_with_category 
FROM jobs 
WHERE is_active = 1 
  AND job_category_id IS NOT NULL 
  AND expiry_date > NOW();

-- Check which job categories have active jobs
SELECT 
    jc.job_category_id,
    jc.job_category,
    COUNT(j.id) as job_count
FROM job_categories jc
LEFT JOIN jobs j ON j.job_category_id = jc.job_category_id 
    AND j.is_active = 1 
    AND j.expiry_date > NOW()
WHERE jc.is_active = 1
GROUP BY jc.job_category_id, jc.job_category
HAVING job_count > 0
ORDER BY job_count DESC;

-- ============================================================================
-- NOTES:
-- ============================================================================
-- 1. These queries are safe to run multiple times (idempotent)
-- 2. The INSERT query will skip any categories that already exist
-- 3. The UPDATE queries only update rows where job_category_id is NULL or 0
-- 4. After running these queries, clear your application cache:
--    - php artisan cache:clear
--    - php artisan view:clear
-- 5. The code changes in app/Job.php will ensure future jobs automatically
--    get job_category_id set when they are created/updated
-- ============================================================================
