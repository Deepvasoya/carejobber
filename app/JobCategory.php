<?php

namespace App;

use App;
use App\Traits\Lang;
use App\Traits\IsDefault;
use App\Traits\Active;
use App\Traits\Sorted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobCategory extends Model
{
    use Lang;
    use IsDefault;
    use Active;
    use Sorted;

    protected $table = 'job_categories';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];

    protected static function booted()
    {
        static::saving(function ($jobCategory) {
            if (empty($jobCategory->slug) && !empty($jobCategory->job_category)) {
                $jobCategory->slug = Str::slug($jobCategory->job_category);
            }
        });
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'job_category_id');
    }

    public function getJobsCountAttribute()
    {
        return $this->jobs()->count();
    }

    public static function getUsingJobCategories($limit = 10)
    {
        $jobCategoryIds = App\Job::select('job_category_id')->pluck('job_category_id')->toArray();
        return App\JobCategory::whereIn('job_category_id', $jobCategoryIds)->lang()->active()->inRandomOrder()->paginate($limit);
    }
}