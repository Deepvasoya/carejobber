<?php
/**
 * Email Template Test Script
 * This script verifies that all email templates are properly configured in the admin
 * and that all Mailable classes are using EmailTemplateService
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\EmailTemplate;
use Illuminate\Support\Facades\DB;

echo "=================================================\n";
echo "EMAIL TEMPLATE VERIFICATION TEST\n";
echo "=================================================\n\n";

// Get all email templates from database
$templates = EmailTemplate::orderBy('slug')->get();

echo "Total templates in database: " . $templates->count() . "\n\n";

// Expected templates based on seeder
$expectedTemplates = [
    'user-registered',
    'company-registered',
    'job-posted-admin',
    'job-posted-company',
    'job-approved',
    'job-applied-company',
    'job-applied-jobseeker',
    'job-application-status',
    'contact-form',
    'chat-message',
    'resume-posted',
    'incomplete-profile',
    'job-alerts',
    'email-to-friend',
    'report-abuse',
    'referral-invite-company',
    'referral-invite-user',
    'package-receipt',
    'password-reset',
    'email-verification',
    'generic-message',
    'job-recommendation-jobseeker',
    'candidate-recommendation-employer',
    'applicant-contact',
    'company-contact',
    'document-account-approved',
    'document-resubmit-request',
    'document-pending-company',
    'document-pending-admin',
    'job-seeker-rejected',
];

echo "Expected templates: " . count($expectedTemplates) . "\n\n";

// Check for missing templates
$existingSlugs = $templates->pluck('slug')->toArray();
$missingTemplates = array_diff($expectedTemplates, $existingSlugs);

if (count($missingTemplates) > 0) {
    echo "❌ MISSING TEMPLATES:\n";
    foreach ($missingTemplates as $slug) {
        echo "   - $slug\n";
    }
    echo "\n";
} else {
    echo "✅ All expected templates exist in database\n\n";
}

// Check for extra templates
$extraTemplates = array_diff($existingSlugs, $expectedTemplates);
if (count($extraTemplates) > 0) {
    echo "ℹ️  EXTRA TEMPLATES (not in expected list):\n";
    foreach ($extraTemplates as $slug) {
        echo "   - $slug\n";
    }
    echo "\n";
}

// Display all templates with their status
echo "=================================================\n";
echo "TEMPLATE DETAILS\n";
echo "=================================================\n\n";

foreach ($templates as $template) {
    $status = $template->is_active ? '✅ Active' : '❌ Inactive';
    echo sprintf("%-40s %s\n", $template->slug, $status);
    echo "   Name: {$template->name}\n";
    echo "   Subject: {$template->subject}\n";
    
    // Check if body has placeholders
    if (strpos($template->body, '{') !== false) {
        preg_match_all('/\{([A-Z_]+)\}/', $template->body, $matches);
        if (!empty($matches[1])) {
            echo "   Shortcodes: " . implode(', ', array_unique($matches[1])) . "\n";
        }
    }
    echo "\n";
}

// Check Mailable classes
echo "=================================================\n";
echo "MAILABLE CLASSES VERIFICATION\n";
echo "=================================================\n\n";

$mailableFiles = glob(__DIR__ . '/app/Mail/*.php');
$mailablesUsingService = [];
$mailablesNotUsingService = [];

foreach ($mailableFiles as $file) {
    $content = file_get_contents($file);
    $className = basename($file, '.php');
    
    // Check if it uses EmailTemplateService
    if (strpos($content, 'EmailTemplateService') !== false) {
        $mailablesUsingService[] = $className;
    } else {
        // Check if it's actually sending emails (not just a base class)
        if (strpos($content, 'function build()') !== false) {
            $mailablesNotUsingService[] = $className;
        }
    }
}

echo "Mailable classes using EmailTemplateService: " . count($mailablesUsingService) . "\n";
foreach ($mailablesUsingService as $class) {
    echo "   ✅ $class\n";
}
echo "\n";

if (count($mailablesNotUsingService) > 0) {
    echo "⚠️  Mailable classes NOT using EmailTemplateService: " . count($mailablesNotUsingService) . "\n";
    foreach ($mailablesNotUsingService as $class) {
        echo "   ❌ $class\n";
    }
    echo "\n";
} else {
    echo "✅ All active Mailable classes use EmailTemplateService\n\n";
}

// Check for direct Mail::send() calls with blade templates
echo "=================================================\n";
echo "CHECKING FOR DIRECT BLADE TEMPLATE USAGE\n";
echo "=================================================\n\n";

$controllerFiles = glob(__DIR__ . '/app/Http/Controllers/**/*.php');
$directBladeCalls = [];

foreach ($controllerFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace(__DIR__ . '/', '', $file);
    
    // Check for Mail::send('emails.
    if (preg_match_all("/Mail::send\('emails\.([^']+)'/", $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $directBladeCalls[] = [
                'file' => $relativePath,
                'template' => $match[1]
            ];
        }
    }
}

if (count($directBladeCalls) > 0) {
    echo "⚠️  Found " . count($directBladeCalls) . " direct blade template calls:\n";
    foreach ($directBladeCalls as $call) {
        echo "   ❌ {$call['file']} -> emails.{$call['template']}\n";
    }
    echo "\n";
} else {
    echo "✅ No direct blade template calls found\n\n";
}

echo "=================================================\n";
echo "TEST COMPLETE\n";
echo "=================================================\n";
