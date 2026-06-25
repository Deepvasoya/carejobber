<?php

namespace App\Services;

use App\Models\Medo\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HtmlJobScraper
{
    private array $cityNames;

    public function __construct()
    {
        $this->cityNames = City::pluck('name')->map(fn($n) => trim($n))->filter()->values()->toArray();
    }

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
            'salary_min' => '',
            'salary_max' => '',
            'external_id' => '',
            'apply_url' => $url,
            'job_primary_location' => '',
            'job_shift' => '',
            'functional_area' => '',
            'union' => '',
            'fte' => '',
            'hours_per_shift' => '',
            'shifts_per_cycle' => '',
            'expiry_date' => '',
            'city_id' => null,
            'city_name' => '',
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

            $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $html);
            $html = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $html);

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);

            $jsonld = $this->extractJsonLd($dom, $xpath);
            $og = $this->extractOpenGraph($xpath);
            $meta = $this->extractMeta($xpath);

            $isCovenant = $this->isCovenantUrl($url, $html);

            if ($isCovenant) {
                $labels = $this->extractLabelValuePairs($dom, $xpath, $html);
                $result['title'] = $this->extractTitle($dom, $xpath, $jsonld, $og);
                $result['description'] = $this->extractDescription($dom, $xpath, $jsonld, $og, $meta);
                $result['company_name'] = 'Covenant Health';
                $result['location'] = $labels['location'] ?? $this->extractLocation($dom, $xpath, $jsonld);
                $result['functional_area'] = $labels['classification'] ?? '';
                $result['job_primary_location'] = $labels['primary_location'] ?? '';
                $result['job_type'] = $labels['employee_class'] ?? $this->extractJobType($dom, $xpath, $jsonld, $html);
                $result['job_shift'] = $labels['shift_pattern'] ?? '';
                $result['expiry_date'] = $labels['posting_end_date'] ?? '';
                $result['salary_min'] = $labels['minimum_salary'] ?? '';
                $result['salary_max'] = $labels['maximum_salary'] ?? '';
                $result['hours_per_shift'] = $labels['hours_per_shift'] ?? '';
                $result['fte'] = $labels['fte'] ?? '';
                $result['union'] = $labels['union'] ?? '';
                $result['shifts_per_cycle'] = $labels['shifts_per_cycle'] ?? '';
                $result['salary'] = $result['salary_min'] && $result['salary_max']
                    ? '$' . $result['salary_min'] . ' - $' . $result['salary_max']
                    : ($result['salary_min'] ? '$' . $result['salary_min'] : '');
            } else {
                $result['title'] = $this->extractTitle($dom, $xpath, $jsonld, $og);
                $result['description'] = $this->extractDescription($dom, $xpath, $jsonld, $og, $meta);
                $result['company_name'] = $this->extractCompany($dom, $xpath, $jsonld, $og);
                $result['location'] = $this->extractLocation($dom, $xpath, $jsonld);
                $result['job_type'] = $this->extractJobType($dom, $xpath, $jsonld, $html);
                $result['salary'] = $this->extractSalary($dom, $xpath, $jsonld, $html);
                $result['job_primary_location'] = $result['location'];

                $labels = $this->extractLabelValuePairs($dom, $xpath, $html);
                $result['functional_area'] = $labels['classification'] ?? '';
                $result['job_shift'] = $labels['shift_pattern'] ?? $labels['job_shift'] ?? '';
                $result['expiry_date'] = $labels['posting_end_date'] ?? $labels['application_deadline'] ?? '';
                $result['salary_min'] = $labels['minimum_salary'] ?? $labels['min_salary'] ?? '';
                $result['salary_max'] = $labels['maximum_salary'] ?? $labels['max_salary'] ?? '';
                $result['hours_per_shift'] = $labels['hours_per_shift'] ?? '';
                $result['fte'] = $labels['fte'] ?? '';
                $result['union'] = $labels['union'] ?? '';
                $result['shifts_per_cycle'] = $labels['shifts_per_cycle'] ?? '';
            }

            $detected = $this->detectCity(
                $result['job_primary_location'] ?: $result['location'],
                $result['title'],
                $result['description']
            );
            $result['city_id'] = $detected['city_id'];
            $result['city_name'] = $detected['city_name'];

            $result['success'] = true;

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            Log::warning('[HtmlJobScraper] Error scraping URL: ' . $e->getMessage());
        }

        return $result;
    }

    private function isCovenantUrl(string $url, string $html): bool
    {
        if (stripos($url, 'covenanthealth') !== false) {
            return true;
        }
        if (stripos($html, 'Covenant Health') !== false && stripos($html, 'Classification') !== false) {
            return true;
        }
        return false;
    }

    private function extractLabelValuePairs(\DOMDocument $dom, \DOMXPath $xpath, string $html): array
    {
        $labels = [];

        $patterns = [
            'classification' => ['Classification', 'Job Category', 'Functional Area', 'Category'],
            'primary_location' => ['Primary Location', 'Job Location', 'Location', 'Work Location'],
            'employee_class' => ['Employee Class', 'Employment Type', 'Job Type', 'Position Type'],
            'shift_pattern' => ['Shift Pattern', 'Shift', 'Schedule', 'Work Schedule'],
            'posting_end_date' => ['Posting End Date', 'Closing Date', 'Application Deadline', 'Apply By', 'Date Posted'],
            'minimum_salary' => ['Minimum Salary', 'Min Salary', 'Min. Salary', 'Salary Min'],
            'maximum_salary' => ['Maximum Salary', 'Max Salary', 'Max. Salary', 'Salary Max'],
            'hours_per_shift' => ['Hours per Shift', 'Hours/Shift', 'Hours Per Shift'],
            'fte' => ['FTE', 'Full Time Equivalent', 'Full-Time Equivalent'],
            'union' => ['Union', 'Union Affiliation', 'Unionized'],
            'shifts_per_cycle' => ['Shifts per Cycle', 'Shifts/Cycle', 'Shifts Per Cycle'],
            'job_shift' => ['Shift', 'Job Shift'],
            'application_deadline' => ['Application Deadline', 'Closing Date', 'Posting End Date'],
            'min_salary' => ['Min. Salary', 'Minimum Salary'],
            'max_salary' => ['Max. Salary', 'Maximum Salary'],
        ];

        // Try extracting from table rows first (common in job boards)
        $trNodes = $xpath->query("//tr");
        foreach ($trNodes as $tr) {
            $cells = $tr->getElementsByTagName('td');
            $ths = $tr->getElementsByTagName('th');
            $labelNodes = $ths->length > 0 ? $ths : $cells;
            if ($labelNodes->length < 2) continue;

            $labelText = $this->clean($labelNodes->item(0)->textContent);
            $valueText = $this->clean($labelNodes->item(1)->textContent);
            if (!$labelText || !$valueText) continue;

            foreach ($patterns as $key => $aliases) {
                if (empty($labels[$key])) {
                    foreach ($aliases as $alias) {
                        if (strcasecmp($labelText, $alias) === 0 || stripos($labelText, $alias) !== false) {
                            $labels[$key] = $valueText;
                            break;
                        }
                    }
                }
            }
        }

        // Try label-value div patterns
        $labelSelectors = [
            "//div[contains(@class, 'field')]//label",
            "//div[contains(@class, 'label')]",
            "//span[contains(@class, 'label')]",
            "//strong",
            "//dt",
        ];

        foreach ($labelSelectors as $sel) {
            $nodes = $xpath->query($sel);
            foreach ($nodes as $node) {
                $labelText = $this->clean($node->textContent);
                if (!$labelText) continue;

                $parent = $node->parentNode;
                $valueNode = null;
                if ($parent) {
                    $sibling = $node->nextSibling;
                    while ($sibling) {
                        if ($sibling->nodeType === XML_TEXT_NODE && trim($sibling->textContent)) {
                            $valueNode = $sibling;
                            break;
                        }
                        if ($sibling->nodeType === XML_ELEMENT_NODE) {
                            $valueNode = $sibling;
                            break;
                        }
                        $sibling = $sibling->nextSibling;
                    }
                    if (!$valueNode) {
                        $children = $parent->getElementsByTagName('*');
                        foreach ($children as $child) {
                            if ($child !== $node) {
                                $valueNode = $child;
                                break;
                            }
                        }
                    }
                }

                $valueText = $valueNode ? $this->clean($valueNode->textContent) : '';
                if (!$valueText) continue;

                foreach ($patterns as $key => $aliases) {
                    if (empty($labels[$key])) {
                        foreach ($aliases as $alias) {
                            if (strcasecmp($labelText, $alias) === 0 || stripos($labelText, $alias) !== false) {
                                $labels[$key] = $valueText;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Regex fallback on raw HTML
        if (count($labels) < 5) {
            $htmlLines = preg_split('/\R/', $html);
            foreach ($htmlLines as $line) {
                $cleanLine = strip_tags($line);
                $cleanLine = $this->clean($cleanLine);
                if (strlen($cleanLine) < 3) continue;

                foreach ($patterns as $key => $aliases) {
                    if (empty($labels[$key])) {
                        foreach ($aliases as $alias) {
                            if (preg_match('/' . preg_quote($alias, '/') . '\s*[:;]\s*(.+)/i', $cleanLine, $m)) {
                                $val = trim($m[1]);
                                $val = preg_replace('/\s+/', ' ', $val);
                                if (strlen($val) > 0 && strlen($val) < 200) {
                                    $labels[$key] = $val;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $labels;
    }

    private function detectCity(string ...$haystacks): array
    {
        $cityNames = $this->cityNames;
        usort($cityNames, fn($a, $b) => strlen($b) - strlen($a));

        foreach ($haystacks as $haystack) {
            if (empty($haystack)) continue;
            $lower = mb_strtolower($haystack);

            foreach ($cityNames as $cityName) {
                $lowerCity = mb_strtolower($cityName);
                if (str_contains($lower, $lowerCity)) {
                    $city = City::where('name', $cityName)->first();
                    if ($city) {
                        return ['city_id' => $city->id, 'city_name' => $city->name];
                    }
                }
            }
        }

        return ['city_id' => null, 'city_name' => ''];
    }

    private function extractJsonLd(\DOMDocument $dom, \DOMXPath $xpath): ?array
    {
        $scripts = $xpath->query("//script[@type='application/ld+json']");
        foreach ($scripts as $script) {
            $json = trim($script->textContent);
            if (empty($json)) continue;
            $data = json_decode($json, true);
            if (!$data) continue;

            if (isset($data['@graph']) && is_array($data['@graph'])) {
                foreach ($data['@graph'] as $item) {
                    if (($item['@type'] ?? '') === 'JobPosting') return $item;
                }
            }

            if (($data['@type'] ?? '') === 'JobPosting') return $data;

            if (isset($data[0]) && is_array($data[0])) {
                foreach ($data as $item) {
                    $type = $item['@type'] ?? '';
                    if ($type === 'JobPosting') return $item;
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
        if ($jsonld && !empty($jsonld['title'])) return $this->clean($jsonld['title']);
        if (!empty($og['title'])) return $this->clean($og['title']);
        $titles = $xpath->query("//title");
        if ($titles->length > 0) {
            $title = trim($titles->item(0)->textContent);
            if ($title) {
                $title = preg_replace('/\s*[|\-–—:]\s*[^|\-–—:]+$/', '', $title);
                return $this->clean($title);
            }
        }
        $h1s = $xpath->query("//h1");
        if ($h1s->length > 0) return $this->clean($h1s->item(0)->textContent);
        return '';
    }

    private function extractDescription(\DOMDocument $dom, \DOMXPath $xpath, ?array $jsonld, array $og, array $meta): string
    {
        if ($jsonld) {
            $desc = $jsonld['description'] ?? $jsonld['responsibilities'] ?? $jsonld['qualifications'] ?? '';
            if ($desc) return $this->clean($desc);
        }
        if (!empty($og['description'])) return $this->clean($og['description']);
        if (!empty($meta['description'])) return $this->clean($meta['description']);
        $metas = $xpath->query("//meta[@name='description']");
        if ($metas->length > 0) {
            $content = $metas->item(0)->getAttribute('content');
            if ($content) return $this->clean($content);
        }
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
            if ($hiring) return $hiring['name'] ?? $hiring['legalName'] ?? '';
        }
        if (!empty($og['site_name'])) return $this->clean($og['site_name']);
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
        if ($jsonld && !empty($jsonld['employmentType'])) return $jsonld['employmentType'];
        $htmlLower = strtolower($html);
        if (preg_match('/employment\s*type[:\s]*([^.<]+)/i', $htmlLower, $m)) return trim($m[1]);
        if (preg_match('/job\s*type[:\s]*([^.<]+)/i', $htmlLower, $m)) return trim($m[1]);
        if (preg_match('/position\s*type[:\s]*([^.<]+)/i', $htmlLower, $m)) return trim($m[1]);
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
        if (str_contains($htmlLower, 'permanent')) return 'Permanent';
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
        if (preg_match('/salary[:\s]*\$?([0-9,]+)\s*[-–to]+\s*\$?([0-9,]+)/i', $html, $m)) return '$' . $m[1] . ' - $' . $m[2];
        if (preg_match('/salary[:\s]*\$?([0-9,]+(\.[0-9]+)?)/i', $html, $m)) return '$' . $m[1];
        if (preg_match('/\$([0-9,]+(\.[0-9]+)?)\s*[-–]\s*\$?([0-9,]+(\.[0-9]+)?)/i', $html, $m)) return '$' . $m[1] . ' - $' . $m[3];
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
