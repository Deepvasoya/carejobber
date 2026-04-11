<?php

namespace App\Http\Controllers;

use DB;
use App\Faq;
use App\FaqCategory;
use App\FaqSection;
use App\Seo;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all active sections with their categories and FAQs
        $sections = FaqSection::select([
                            'faq_sections.id',
                            'faq_sections.name',
                            'faq_sections.slug',
                            'faq_sections.description',
                            'faq_sections.sort_order'
                        ])
                        ->where('faq_sections.is_active', 1)
                        ->lang()
                        ->orderBy('faq_sections.sort_order', 'ASC')
                        ->orderBy('faq_sections.id', 'ASC')
                        ->with(['categories' => function($query) {
                            $query->select([
                                'faq_categories.id',
                                'faq_categories.faq_section_id',
                                'faq_categories.name',
                                'faq_categories.slug',
                                'faq_categories.description',
                                'faq_categories.sort_order'
                            ])
                            ->where('faq_categories.is_active', 1)
                            ->lang()
                            ->orderBy('faq_categories.sort_order', 'ASC')
                            ->orderBy('faq_categories.id', 'ASC')
                            ->withCount(['faqs' => function($q) {
                                $q->lang();
                            }])
                            ->with(['faqs' => function($q) {
                                $q->select([
                                    'faqs.id',
                                    'faqs.faq_category_id',
                                    'faqs.faq_question',
                                    'faqs.faq_answer',
                                    'faqs.sort_order'
                                ])
                                ->lang()
                                ->orderBy('faqs.sort_order', 'ASC')
                                ->orderBy('faqs.id', 'ASC');
                            }]);
                        }])
                        ->get();
        
        $seo = SEO::where('seo.page_title', 'like', 'faq')->first();
        
        return view('faq.list_faq')
                ->with('sections', $sections)
                ->with('seo', $seo);
    }

}
