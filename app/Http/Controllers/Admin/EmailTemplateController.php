<?php

namespace App\Http\Controllers\Admin;

use Auth;
use DB;
use Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\EmailTemplate;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Http\Requests\EmailTemplateFormRequest;
use App\Http\Controllers\Controller;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates.
     */
    public function indexEmailTemplates()
    {
        $categories = EmailTemplate::select('category')
            ->distinct()
            ->pluck('category')
            ->toArray();
        
        return view('admin.email_template.index')->with('categories', $categories);
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function editEmailTemplate($id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);
        return view('admin.email_template.edit')
                        ->with('emailTemplate', $emailTemplate);
    }

    /**
     * Update the specified email template in storage.
     */
    public function updateEmailTemplate($id, EmailTemplateFormRequest $request)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);
        $emailTemplate->name = $request->input('name');
        $emailTemplate->subject = $request->input('subject');
        $emailTemplate->body = $request->input('body');
        $emailTemplate->is_active = $request->input('is_active', 1);
        $emailTemplate->update();
        
        flash('Email Template has been updated!')->success();
        return \Redirect::route('edit.email.template', array($emailTemplate->id));
    }

    /**
     * Fetch email templates data for DataTables.
     */
    public function fetchEmailTemplatesData(Request $request)
    {
        $emailTemplates = EmailTemplate::select(
                        [
                            'email_templates.id',
                            'email_templates.name',
                            'email_templates.slug',
                            'email_templates.subject',
                            'email_templates.category',
                            'email_templates.is_active',
                            'email_templates.created_at',
                            'email_templates.updated_at'
                        ]
        );
        
        return Datatables::of($emailTemplates)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('name') && !empty($request->name)) {
                                $query->where('email_templates.name', 'like', "%{$request->get('name')}%");
                            }
                            if ($request->has('category') && !empty($request->get('category'))) {
                                $query->where('email_templates.category', 'like', "%{$request->get('category')}%");
                            }
                        })
                        ->addColumn('name', function ($emailTemplate) {
                            return '<strong>' . $emailTemplate->name . '</strong><br><small style="color: #666;">' . $emailTemplate->slug . '</small>';
                        })
                        ->addColumn('category', function ($emailTemplate) {
                            $badges = [
                                'user' => 'primary',
                                'company' => 'info',
                                'job' => 'success',
                                'application' => 'warning',
                                'notification' => 'secondary',
                                'contact' => 'dark',
                                'messaging' => 'info',
                                'sharing' => 'primary',
                                'moderation' => 'danger',
                                'referral' => 'success',
                                'payment' => 'warning',
                                'authentication' => 'danger',
                                'general' => 'secondary'
                            ];
                            $badge = $badges[$emailTemplate->category] ?? 'secondary';
                            return '<span class="badge bg-' . $badge . '">' . ucfirst($emailTemplate->category) . '</span>';
                        })
                        ->addColumn('is_active', function ($emailTemplate) {
                            return $emailTemplate->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                        })
                        ->addColumn('action', function ($emailTemplate) {
                            return '
                <div class="btn-group">
                    <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action
                        <i class="ri ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="' . route('edit.email.template', ['id' => $emailTemplate->id]) . '"><i class="ri ri-pencil-line me-1"></i>Edit</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="' . route('preview.email.template', ['id' => $emailTemplate->id]) . '" target="_blank"><i class="ri ri-eye-line me-1"></i>Preview</a>
                        </li>
                    </ul>
                </div>';
                        })
                        ->rawColumns(['name', 'category', 'is_active', 'action'])
                        ->setRowId(function($emailTemplate) {
                            return 'email_template_dt_row_' . $emailTemplate->id;
                        })
                        ->make(true);
    }

    /**
     * Preview email template
     */
    public function previewEmailTemplate($id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);
        $shortcodes = $emailTemplate->getShortcodesArray();
        
        // Create sample data for preview
        $sampleData = [];
        foreach ($shortcodes as $shortcode => $description) {
            $key = str_replace(['{', '}'], '', $shortcode);
            $sampleData[$key] = '[' . $description . ']';
        }
        
        $parsed = $emailTemplate->parseShortcodes($sampleData);
        
        return view('admin.email_template.preview')
                        ->with('emailTemplate', $emailTemplate)
                        ->with('subject', $parsed['subject'])
                        ->with('body', $parsed['body']);
    }

    /**
     * Reset email template to default
     */
    public function resetEmailTemplate(Request $request)
    {
        $id = $request->input('id');
        try {
            // Run seeder to reset template
            \Artisan::call('db:seed', ['--class' => 'EmailTemplatesSeeder']);
            echo 'ok';
        } catch (\Exception $e) {
            echo 'notok';
        }
    }
}
