# Functional Area to Job Category Migration

## Overview
This document outlines the consolidation of `functional_area` into `job_category` throughout the application. The functional area concept was confusing and duplicative, so we're standardizing on job categories.

## Changes Completed

### 1. Database Migration
- **File**: `database/migrations/2026_05_03_000000_migrate_functional_area_to_job_category.php`
- Copies all functional_areas data to job_categories table
- Adds `job_category_id` column to jobs table
- Migrates data from `functional_area_id` to `job_category_id` in both jobs and users tables
- Preserves functional_area_id columns for now (can be dropped after verification)

### 2. Recommendation System Updated
- **SendJobRecommendationsListener**: Now matches job seekers based on `job_category_id` instead of `functional_area_id`
- **SendCandidateRecommendationsListener**: Now matches candidates to employers based on `job_category_id`
- **HomeController::recommendedJobsQuery()**: Updated to use `user->job_category_id`

### 3. Forms Updated
- **Job Post Form** (`resources/views/job/inc/job.blade.php`): Changed from functional_area_id to job_category_id
- **User Profile Form** (`resources/views/user/inc/profile.blade.php`): Changed from functional_area_id to job_category_id
- **Admin Job Form** (`resources/views/admin/job/forms/form.blade.php`): Changed from functional_area_id to job_category_id
- **Admin User Form** (`resources/views/admin/user/forms/form.blade.php`): Changed from functional_area_id to job_category_id
- **Registration Form** (`resources/views/auth/register.blade.php`): Already using job_category_id

### 4. Routes Updated
- **routes/admin.php**: Removed `include_once($real_path . 'functional_area.php');`
- Job category routes remain active
- **Admin Sidebar**: Removed "Functional Areas" menu item, kept "Job Categories"
- **Deleted**: `resources/views/admin/shared/side_bars/functional_area.blade.php`

## Files That Still Need Updates

### Controllers (Need to replace $functionalAreas with $jobCategories)
1. `app/Http/Controllers/UserController.php` - Line 98
2. `app/Http/Controllers/Admin/UserController.php` - Lines 104, 204
3. `app/Http/Controllers/Job/JobController.php` - Line 64
4. `app/Http/Controllers/Job/JobSeekerController.php` - Line 35
5. `app/Http/Controllers/Api/JobController.php` - Line 60
6. `app/Http/Controllers/Api/JobSeekerController.php` - Line 35
7. `app/Http/Controllers/IndexController.php` - Line 84
8. `app/Traits/JobTrait.php` - Lines 325, 435, 568, 733
9. `app/Traits/JobApiTrait.php` - Lines 269, 436

### Services
1. `app/Services/UserSubmittedLookupService.php` - Update custom_functional_area to custom_job_category
2. `app/Services/ProfileJobTitleMatching.php` - Update functional_area_id references

### Views (Need to update remaining references)
1. `resources/views/includes/search_form_without_company.blade.php`
2. `resources/views/admin/user/list.blade.php`
3. `resources/views/admin/jobB/forms/form.blade.php`
4. All views in `Update/update-6.3/resources/views/` directory

### Models
1. `app/Job.php` - Add jobCategory() relationship, update getFunctionalArea() or replace with getJobCategory()
2. `app/User.php` - Already has jobCategory() relationship

### API & Traits
1. `app/Traits/CommonUserFunctions.php` - Line 112
2. `app/Traits/JobApiTrait.php` - Lines 145, 195, 329, 498, 582-583

## Migration Steps

### Step 1: Run the Migration
```bash
php artisan migrate
```

This will:
- Copy all functional_areas to job_categories
- Add job_category_id to jobs table
- Migrate existing data

### Step 2: Update Controllers
Replace all instances of:
```php
$functionalAreas = DataArrayHelper::langFunctionalAreasArray();
// or
$functionalAreas = DataArrayHelper::defaultFunctionalAreasArray();
```

With:
```php
$jobCategories = DataArrayHelper::langJobCategoriesArray();
// or
$jobCategories = DataArrayHelper::defaultJobCategoriesArray();
```

### Step 3: Update DataArrayHelper
Ensure `DataArrayHelper` has methods:
- `langJobCategoriesArray()`
- `defaultJobCategoriesArray()`

### Step 4: Update JavaScript
Search for JavaScript that references:
- `functional_area_id`
- `custom_functional_area`

Replace with:
- `job_category_id`
- `custom_job_category`

### Step 5: Test Thoroughly
1. Test job posting with category selection
2. Test user profile update with category
3. Test job recommendations for seekers
4. Test candidate recommendations for employers
5. Test admin job/user management
6. Test search functionality

### Step 6: Drop Old Columns (After Verification)
Once everything is working, uncomment the drop column statements in the migration:
```php
Schema::table('jobs', function (Blueprint $table) {
    $table->dropColumn('functional_area_id');
});

Schema::table('users', function (Blueprint $table) {
    $table->dropColumn('functional_area_id');
});
```

### Step 7: Remove Functional Area Admin Section
1. Delete `routes/admin_routes/functional_area.php`
2. Delete `app/Http/Controllers/Admin/FunctionalAreaController.php`
3. Delete `resources/views/admin/functional_area/` directory
4. Delete `app/FunctionalArea.php` model
5. Remove functional area menu items from admin navigation

## Benefits
1. **Simplified**: One category system instead of two confusing ones
2. **Consistent**: All forms use the same field name
3. **Better UX**: Users understand "Job Category" better than "Functional Area"
4. **Easier Maintenance**: Less code duplication

## Rollback Plan
If issues arise, the migration's `down()` method will:
1. Restore functional_area_id columns
2. Copy data back from job_category_id
3. Restore original state

## Notes
- The functional_areas table is preserved for now
- Old functional_area_id columns are preserved until verification
- All data is safely migrated before any deletions
