<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HtmlJobScraper
{
    public function scrape(string $url): array
    {
        $result = [
            'success' => false,
            'title' => '',
            'description' => '',
            'company_name' => '',
            'location' => '',
            'job_type' => '',
            'salary' => '',
            'external_id' => '',
            'apply_url' => $url,
            'error' => '',
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ])
                ->get($url);

            if (!$response->successful()) {
                $result['error'] = "HTTP {$response->status()} - Failed to fetch URL";
                return $result;
            }

            $html = $response->body();
            if (empty($html)) {
                $result['error'] = 'Empty response body';
                return $result;
            }

            // Strip encoding-borked chars before parsing
            $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $html);
            $html = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $html);

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);

            // 1. Try JSON-LD first (most reliable for job postings)
            $jsonld = $this->extractJsonLd($dom, $xpath);

            // 2. Extract Open Graph tags
            $og = $this->extractOpenGraph($xpath);

            // 3. Extract meta tags
            $meta = $this->extractMeta($xpath);

            // 4. Extract title
            $title = $this->extractTitle($dom, $xpath, $jsonld, $og);

            // 5. Extract description
            $description = $this->extractDescription($dom, $xpath, $jsonld, $og, $meta);

            // 6. Extract company name
            $company = $this->extractCompany($dom, $xpath, $jsonld, $og);

            // 7. Extract location
            $location = $this->extractLocation($dom, $xpath, $jsonld);

            // 8. Extract job type
            $jobType = $this->extractJobType($dom, $xpath, $jsonld, $html);

            // 9. Extract salary
            $salary = $this->extractSalary($dom, $xpath, $jsonld, $html);

            $result['success'] = true;
            $result['title'] = $title;
            $result['description'] = $description;
            $result['company_name'] = $company;
            $result['location'] = $location;
            $result['job_type'] = $jobType;
            $result['salary'] = $salary;

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            Log::warning('[HtmlJobScraper] Error scraping URL: ' . $e->getMessage());
        }

        return $result;
    }

    private function extractJsonLd(\DOMDocument $dom, \DOMXPath $xpath): ?array
    {
        $scripts = $xpath->query("//script[@type='application/ld+json']");
        foreach ($scripts as $script) {
            $json = trim($script->textContent);
            if (empty($json)) continue;

            $data = json_decode($json, true);
            if (!$data) continue;

            // Handle @graph array
            if (isset($data['@graph']) && is_array($data['@graph'])) {
                foreach ($data['@graph'] as $item) {
                    if (($item['@type'] ?? '') === 'JobPosting') {
                        return $item;
                    }
                }
            }

            if (($data['@type'] ?? '') === 'JobPosting') {
                return $data;
            }

            // Handle multiple items in array
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($data as $item) {
                    $type = $item['@type'] ?? '';
                    if ($type === 'JobPosting' || $type === 'JobPosting') {
                        return $item;
                    }
                }
            }
        }
        return null;
    }

    private function extractOpenGraph(\DOMXPath $xpath): array
    {
        $og = [];
        $metas = $xpath->query("//meta[starts-with(@property, 'og:')]");
        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');
            if ($property && $content) {
                $og[str_replace('og:', '', $property)] = $content;
            }
        }
        return $og;
    }

    private function extractMeta(\DOMXPath $xpath): array
    {
        $meta = [];
        $metas = $xpath->query("//meta[@name]");
        foreach ($metas as $m) {
            $name = $m->getAttribute('name');
            $content = $m->getAttribute('content');
            if ($name && $content) {
                $meta[$name] = $content;
            }
        }
        return $meta;
    }

    private function extractTitle(\DOMDocument $dom, \DOMXPath $xpath, ?array $jsonld, array $og): string
    {
        // From JSON-LD
        if ($jsonld && !empty($jsonld['title'])) {
            return $this->clean($jsonld['title']);
        }

        // From Open Graph
        if (!empty($og['title'])) {
            return $this->clean($og['title']);
        }

        // From <title> tag
        $titles = $xpath->query("//title");
        if ($titles->length > 0) {
            $title = trim($titles->item(0)->textContent);
            if ($title) {
                // Remove site name suffix like " - Company Name"
                $title = preg_replace('/\s*[|\-–—:]\s*[^|\-–—:]+$/', '', $title);
                return $this->clean($title);
            }
        }

        // From <h1>
        $h1s = $xpath->query("//h1");
        if ($h1s->length > 0) {
            return $this->clean($h1s->item(0)->textContent);
        }

        return '';
    }

    private function extractDescription(\DOMDocument $dom, \DOMXPath $xpath, ?array $jsonld, array $og, array $meta): string
    {
        // From JSON-LD
        if ($jsonld) {
            $desc = $jsonld['description'] ?? $jsonld['responsibilities'] ?? $jsonld['qualifications'] ?? '';
            if ($desc) {
                return $this->clean($desc);
            }
        }

        // From Open Graph
        if (!empty($og['description'])) {
            return $this->clean($og['description']);
        }

        // From meta description
        if (!empty($meta['description'])) {
            return $this->clean($meta['description']);
        }

        // From <meta name="description">
        $metas = $xpath->query("//meta[@name='description']");
        if ($metas->length > 0) {
            $content = $metas->item(0)->getAttribute('content');
            if ($content) return $this->clean($content);
        }

        // Fallback: try to get job description div
        $selectors = [
            "//div[contains(@class, 'job-description')]",
            "//div[contains(@class, 'job_description')]",
            "//div[contains(@class, 'description')]",
            "//div[contains(@id, 'job-description')]",
            "//section[contains(@class, 'job-description')]",
            "//div[contains(@class, 'posting')]",
        ];

        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                $text = $this->getInnerHtml($nodes->item(0));
                $text = strip_tags($text, '<p><br><ul><ol><li><b><strong><em><i><h3><h4><h5>');
                $text = $this->clean($text);
                if (strlen($text) > 50) return $text;
            }
        }

        return '';
    }

    private function extractCompany(\DOMDocument $dom, \DOMXPath $xpath, ?array $jsonld, array $og): string
    {
        if ($jsonld) {
            $hiring = $jsonld['hiringOrganization'] ?? null;
            if ($hiring) {
                return $hiring['name'] ?? $hiring['legalName'] ?? '';
            }
        }

        if (!empty($og['site_name'])) {
            return $this->clean($og['site_name']);
        }

        $selectors = [
            "//span[contains(@class, 'company-name')]",
            "//div[contains(@class, 'company-name')]",
            "//a[contains(@class, 'company-name')]",
            "//span[contains(@class, 'employer')]",
            "//div[contains(@class, 'employer')]",
            "//meta[@itemprop='name']",
            "//span[@itemprop='name']",
            "//meta[@itemprop='hiringOrganization']",
        ];

        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                $text = $nodes->item(0)->getAttribute('content') ?: $nodes->item(0)->textContent;
                if ($text = trim($text)) return $this->clean($text);
            }
        }

        return '';
    }

    private function extractLocation(\DOMDocument $dom, \DOMXPath $xpath, ?array $jsonld): string
    {
        if ($jsonld) {
            $loc = $jsonld['jobLocation'] ?? null;
            if ($loc) {
                $address = $loc['address'] ?? $loc;
                $parts = [];
                if (!empty($address['addressLocality'])) $parts[] = $address['addressLocality'];
                if (!empty($address['addressRegion'])) $parts[] = $address['addressRegion'];
                if (!empty($address['addressCountry'])) $parts[] = $address['addressCountry'];
                if ($parts) return implode(', ', $parts);
                if (is_string($address)) return $address;
            }
        }

        $selectors = [
            "//span[contains(@class, 'location')]",
            "//div[contains(@class, 'location')]",
            "//meta[@itemprop='jobLocation']",
            "//span[@itemprop='addressLocality']",
            "//span[@itemprop='addressRegion']",
        ];

        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                $text = $nodes->item(0)->getAttribute('content') ?: $nodes->item(0)->textContent;
                if ($text = trim($text)) return $this->clean($text);
            }
        }

        return '';
    }

    private function extractJobType(\DOMDocument $dom, \DOMXPath $xpath, ?array $jsonld, string $html): string
    {
        if ($jsonld && !empty($jsonld['employmentType'])) {
            return $jsonld['employmentType'];
        }

        $htmlLower = strtolower($html);

        if (preg_match('/employment\s*type[:\s]*([^.<]+)/i', $htmlLower, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/job\s*type[:\s]*([^.<]+)/i', $htmlLower, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/position\s*type[:\s]*([^.<]+)/i', $htmlLower, $m)) {
            return trim($m[1]);
        }

        $selectors = [
            "//span[contains(@class, 'job-type')]",
            "//li[contains(@class, 'job-type')]",
            "//span[contains(@class, 'employment-type')]",
            "//meta[@itemprop='employmentType']",
        ];

        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                $text = $nodes->item(0)->getAttribute('content') ?: $nodes->item(0)->textContent;
                if ($text = trim($text)) return $this->clean($text);
            }
        }

        if (str_contains($htmlLower, 'full-time') || str_contains($htmlLower, 'full time')) return 'Full-Time';
        if (str_contains($htmlLower, 'part-time') || str_contains($htmlLower, 'part time')) return 'Part-Time';
        if (str_contains($htmlLower, 'casual') || str_contains($htmlLower, 'on call')) return 'Casual';
        if (str_contains($htmlLower, 'contract') || str_contains($htmlLower, 'temporary')) return 'Contract';
        if (str_contains($htmlLower, 'permanent') || str_contains($htmlLower, 'permanent')) return 'Permanent';

        return '';
    }

    private function extractSalary(\DOMDocument $dom, \DOMXPath $xpath, ?array $jsonld, string $html): string
    {
        if ($jsonld) {
            $comp = $jsonld['baseSalary'] ?? $jsonld['salary'] ?? null;
            if ($comp) {
                $value = $comp['value'] ?? $comp;
                if (is_array($value)) {
                    $min = $value['minValue'] ?? $value['min'] ?? '';
                    $max = $value['maxValue'] ?? $value['max'] ?? '';
                    $currency = $value['currency'] ?? $comp['currency'] ?? '';
                    $unit = $value['unitText'] ?? $comp['unitText'] ?? '';
                    $parts = array_filter([$currency, $min && $max ? "$min-$max" : ($min ?: $max), $unit]);
                    return implode(' ', $parts);
                }
            }
        }

        if (preg_match('/salary[:\s]*\$?([0-9,]+)\s*[-–to]+\s*\$?([0-9,]+)/i', $html, $m)) {
            return '$' . $m[1] . ' - $' . $m[2];
        }
        if (preg_match('/salary[:\s]*\$?([0-9,]+(\.[0-9]+)?)/i', $html, $m)) {
            return '$' . $m[1];
        }
        if (preg_match('/\$([0-9,]+(\.[0-9]+)?)\s*[-–]\s*\$?([0-9,]+(\.[0-9]+)?)/i', $html, $m)) {
            return '$' . $m[1] . ' - $' . $m[3];
        }

        return '';
    }

    private function getInnerHtml(\DOMNode $node): string
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }
        return $html;
    }

    private function clean(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
