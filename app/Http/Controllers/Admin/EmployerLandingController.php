<?php

namespace App\Http\Controllers\Admin;

use App\Cms;
use App\CmsContent;
use App\Helpers\MiscHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployerLandingController extends Controller
{
    /**
     * Display the employer landing page editor form.
     * 
     * Loads the current content for the employer landing page from the CMS.
     * If no content exists, provides empty form for initial content creation.
     * 
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Get the employer landing page CMS entry
        $cms = Cms::where('page_slug', 'employer-landing-page')->first();
        
        if (!$cms) {
            flash('Employer landing page CMS entry not found. Please create it first.')->error();
            return redirect()->route('admin.home');
        }
        
        // Get the content for the current language
        $lang = config('default_lang');
        $cmsContent = CmsContent::where('page_id', $cms->id)
            ->where('lang', $lang)
            ->first();
        
        // If no content exists for this language, create a new instance
        if (!$cmsContent) {
            $cmsContent = new CmsContent();
            $cmsContent->page_id = $cms->id;
            $cmsContent->lang = $lang;
            $cmsContent->page_title = 'Employer Zone';
            $cmsContent->page_content = '';
        }
        
        $direction = MiscHelper::getLangDirection($lang);
        
        return view('admin.employer_landing.edit', compact('cmsContent', 'cms', 'lang', 'direction'));
    }
    
    /**
     * Update the employer landing page content.
     * 
     * Validates and sanitizes the HTML content before saving to prevent XSS attacks.
     * Updates or creates the CMS content entry for the employer landing page.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate the input
        $request->validate([
            'page_title' => 'required|string|max:255',
            'page_content' => 'nullable|string|max:1048576', // 1MB max
            'page_id' => 'required|exists:cms,id',
            'lang' => 'required|string|max:10',
        ]);
        
        // Sanitize the HTML content
        $pageContent = (string) $request->input('page_content', '');
        $sanitizedContent = trim($pageContent) === '' ? null : $this->sanitizeHtml($pageContent);
        
        // Check if content already exists
        $cmsContent = CmsContent::where('page_id', $request->input('page_id'))
            ->where('lang', $request->input('lang'))
            ->first();
        
        if ($cmsContent) {
            // Update existing content
            $cmsContent->page_title = $request->input('page_title');
            $cmsContent->page_content = $sanitizedContent;
            $cmsContent->update();
            
            flash('Employer landing page has been updated successfully!')->success();
        } else {
            // Create new content
            $cmsContent = new CmsContent();
            $cmsContent->page_id = $request->input('page_id');
            $cmsContent->page_title = $request->input('page_title');
            $cmsContent->page_content = $sanitizedContent;
            $cmsContent->lang = $request->input('lang');
            $cmsContent->save();
            
            flash('Employer landing page content has been created successfully!')->success();
        }
        
        return redirect()->route('admin.employer-landing.edit');
    }
    
    /**
     * Sanitize HTML content to prevent XSS attacks.
     * 
     * Removes dangerous elements like script tags, event handlers,
     * and javascript: protocols while preserving safe HTML formatting.
     * 
     * @param string $html
     * @return string
     */
    private function sanitizeHtml(string $html): string
    {
        // Define allowed HTML tags
        $allowedTags = '<div><section><article><header><footer><main><nav><aside>' .
                       '<h1><h2><h3><h4><h5><h6><p><span><strong><em><b><i><u>' .
                       '<ul><ol><li><a><img><br><hr>' .
                       '<table><thead><tbody><tfoot><tr><td><th>' .
                       '<blockquote><pre><code>' .
                       '<figure><figcaption><video><audio><source>';
        
        // Strip dangerous tags
        $sanitized = strip_tags($html, $allowedTags);
        
        // Remove dangerous attributes using regex
        // Remove onclick, onerror, onload, and other event handlers
        $sanitized = preg_replace('/\s*on\w+\s*=\s*["\'].*?["\']/i', '', $sanitized);
        
        // Remove javascript: protocol from href and src attributes
        $sanitized = preg_replace('/(<[^>]+(?:href|src)\s*=\s*["\'])javascript:/i', '$1', $sanitized);
        
        // Remove data: protocol from src attributes (can be used for XSS)
        $sanitized = preg_replace('/(<[^>]+src\s*=\s*["\'])data:/i', '$1', $sanitized);
        
        return $sanitized;
    }
}
