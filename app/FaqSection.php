<?php

namespace App;

use App\Traits\Lang;
use App\Traits\Active;
use App\Traits\Sorted;
use Illuminate\Database\Eloquent\Model;

class FaqSection extends Model
{
    use Lang;
    use Active;
    use Sorted;

    protected $table = 'faq_sections';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the categories for the section.
     */
    public function categories()
    {
        return $this->hasMany(FaqCategory::class, 'faq_section_id', 'id');
    }

    /**
     * Get active categories with their FAQs count
     */
    public function activeCategoriesWithCount()
    {
        return $this->categories()
            ->where('is_active', 1)
            ->withCount(['faqs' => function($query) {
                $query->lang();
            }])
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC');
    }
}
