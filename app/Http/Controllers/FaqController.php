<?php

namespace App\Http\Controllers;

use DB;
use App\Faq;
use App\FaqCategory;
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
        // Get all active categories with their FAQs
        $categories = FaqCategory::select([
                            'faq_categories.id',
                            'faq_categories.name',
                            'faq_categories.description',
                            'faq_categories.sort_order'
                        ])
                        ->where('faq_categories.is_active', 1)
                        ->lang()
                        ->orderBy('faq_categories.sort_order', 'ASC')
                        ->orderBy('faq_categories.id', 'ASC')
                        ->with(['faqs' => function($query) {
                            $query->select([
                                'faqs.id',
                                'faqs.faq_category_id',
                                'faqs.faq_question',
                                'faqs.faq_answer',
                                'faqs.sort_order'
                            ])
                            ->lang()
                            ->orderBy('faqs.sort_order', 'ASC')
                            ->orderBy('faqs.id', 'ASC');
                        }])
                        ->get();
        
        // Get uncategorized FAQs
        $uncategorizedFaqs = Faq::select([
                            'faqs.id',
                            'faqs.faq_question',
                            'faqs.faq_answer',
                            'faqs.sort_order'
                        ])
                        ->whereNull('faqs.faq_category_id')
                        ->lang()
                        ->orderBy('faqs.sort_order', 'ASC')
                        ->orderBy('faqs.id', 'ASC')
                        ->get();
        
        $seo = SEO::where('seo.page_title', 'like', 'faq')->first();
        
        return view('faq.list_faq')
                ->with('categories', $categories)
                ->with('uncategorizedFaqs', $uncategorizedFaqs)
                ->with('seo', $seo);
    }

}
