<?php

namespace App\Http\Controllers;

use App\Cms;
use App\CmsContent;
use App\Company;
use App\Job;

class EmployerLandingController extends Controller
{
    public function index()
    {
        $cms = Cms::where('page_slug', 'employer-landing-page')->first();
        $pageTitle = 'Employer Zone';
        $customContent = null;

        if ($cms) {
            $cmsContent = CmsContent::getContentByPageId($cms->id);

            if ($cmsContent) {
                $pageTitle = $cmsContent->page_title ?: $pageTitle;

                if (trim((string) $cmsContent->page_content) !== '') {
                    $customContent = $this->sanitizeHtml($cmsContent->page_content);
                }
            }
        }

        $seo = (object) [
            'seo_title' => $cms->seo_title ?? 'Employer Zone - Post Jobs & Hire Talent',
            'seo_description' => $cms->seo_description ?? 'Join our employer platform to post jobs, access qualified candidates, and grow your team.',
            'seo_keywords' => $cms->seo_keywords ?? 'employer, post job, hire, recruitment',
            'seo_other' => $cms->seo_other ?? '',
        ];

        $stats = [
            'active_jobs' => Job::where('is_active', 1)->count(),
            'active_employers' => Company::where('is_active', 1)->count(),
            'featured_employers' => Company::where('is_active', 1)->where('is_featured', 1)->count(),
        ];

        return view('employer.landing', [
            'customContent' => $customContent,
            'pageTitle' => $pageTitle,
            'seo' => $seo,
            'stats' => $stats,
        ]);
    }

    private function sanitizeHtml(string $html): string
    {
        $allowedTags = '<div><section><article><header><footer><main><nav><aside>' .
            '<h1><h2><h3><h4><h5><h6><p><span><strong><em><b><i><u>' .
            '<ul><ol><li><a><img><br><hr>' .
            '<table><thead><tbody><tfoot><tr><td><th>' .
            '<blockquote><pre><code>' .
            '<button><form><input><label><select><option><textarea>';

        $sanitized = strip_tags($html, $allowedTags);
        $sanitized = preg_replace('/\s*on\w+\s*=\s*["\'].*?["\']/i', '', $sanitized);
        $sanitized = preg_replace('/(<[^>]+(?:href|src)\s*=\s*["\'])javascript:/i', '$1', $sanitized);
        $sanitized = preg_replace('/(<[^>]+src\s*=\s*["\'])data:/i', '$1', $sanitized);

        return $sanitized;
    }
}
