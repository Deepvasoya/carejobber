<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DocumentUploadService;
use App\VerificationDocument;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Document upload service instance
     *
     * @var DocumentUploadService
     */
    protected $documentService;

    /**
     * Create a new controller instance.
     *
     * @param DocumentUploadService $documentService
     */
    public function __construct(DocumentUploadService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Show the document upload form
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showUploadForm()
    {
        $company = Auth::guard('company')->user();
        $documents = $company->verificationDocuments()->orderBy('uploaded_at', 'desc')->get();
        $latestVerificationDocuments = $documents->groupBy('document_type')->map(function ($group) {
            return $group->first();
        });

        return view('company.verification.upload', compact('company', 'documents', 'latestVerificationDocuments'));
    }

    /**
     * Handle document upload submission
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $company = Auth::guard('company')->user();
        $documentMap = [
            'business_registration' => [
                'type' => VerificationDocument::TYPE_BUSINESS_REGISTRATION,
                'label' => 'Business registration',
            ],
            'tax_document' => [
                'type' => VerificationDocument::TYPE_TAX_DOCUMENT,
                'label' => 'Tax document',
            ],
            'establishment_photo' => [
                'type' => VerificationDocument::TYPE_ESTABLISHMENT_PHOTO,
                'label' => 'Establishment photo',
            ],
        ];
        $uploadedFields = collect(array_keys($documentMap))
            ->filter(function ($field) use ($request) {
                return $request->hasFile($field);
            })
            ->values();

        if ($uploadedFields->isEmpty()) {
            return back()->withErrors([
                'document_upload' => __('Please choose a document to upload.'),
            ])->withInput();
        }

        $rules = [];
        $messages = [];
        foreach ($uploadedFields as $field) {
            $label = $documentMap[$field]['label'];
            $rules[$field] = 'file|mimes:png,jpg,jpeg,pdf|max:2048';
            $messages[$field . '.mimes'] = __($label . ' must be png, jpg, jpeg, or pdf');
            $messages[$field . '.max'] = __($label . ' must not exceed 2MB');
        }

        $request->validate($rules, $messages);

        // Scan all uploaded files for malware
        foreach ($uploadedFields as $field) {
            $file = $request->file($field);

            if (!$this->documentService->scanForMalware($file)) {
                return back()->withErrors([
                    $field => 'File contains malicious content and cannot be uploaded'
                ])->withInput();
            }
        }

        foreach ($uploadedFields as $field) {
            $this->documentService->storeDocument(
                $company->id,
                $documentMap[$field]['type'],
                $request->file($field)
            );
        }

        if ($company->hasBusinessRegistration()) {
            $company->verified = null;
            $company->verified_at = null;
            $company->verification_status = 'pending';
            $company->verification_rejection_reason = null;
            $company->verification_reviewed_at = null;
            $company->save();

            return redirect()->back()
                ->with('success', __('Verification document uploaded successfully. Your verification is now under review.'));
        }

        return redirect()->back()
            ->with('success', __('Document uploaded successfully.'));
    }

    /**
     * Retrieve and display a verification document
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        // Find the document
        $document = VerificationDocument::findOrFail($id);

        // Check authorization: requester must be document owner or admin
        $company = Auth::guard('company')->user();
        $admin = Auth::guard('admin')->user();

        $isOwner = $company && $company->id === $document->company_id;
        $isAdmin = $admin !== null;

        if (!$isOwner && !$isAdmin) {
            abort(403, 'You do not have permission to access this document');
        }

        // Decrypt document data
        $fileData = $document->getDecryptedFileData();

        // Return document with appropriate headers
        return response($fileData)
            ->header('Content-Type', $document->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $document->original_filename . '"')
            ->header('Content-Length', $document->file_size);
    }

}
