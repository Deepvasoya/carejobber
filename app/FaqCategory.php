<?php

namespace App;

use App\Traits\Lang;
use App\Traits\IsDefault;
use App\Traits\Active;
use App\Traits\Sorted;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use Lang;
    use IsDefault;
    use Active;
    use Sorted;

    protected $table = 'faq_categories';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the section that owns the category.
     */
    public function section()
    {
        return $this->belongsTo(FaqSection::class, 'faq_section_id', 'id');
    }

    /**
     * Get the FAQs for the category.
     */
    public function faqs()
    {
        return $this->hasMany(Faq::class, 'faq_category_id', 'id');
    }
}
