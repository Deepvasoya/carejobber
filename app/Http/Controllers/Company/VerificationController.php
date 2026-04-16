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

        return view('company.verification.upload', compact('company', 'documents'));
    }

    /**
     * Handle document upload submission
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'business_registration' => 'required|file|mimes:png,jpg,jpeg,pdf|max:2048',
            'tax_document' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
            'establishment_photo' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048'
        ], [
            'business_registration.required' => 'Business registration document is required',
            'business_registration.mimes' => 'Business registration must be png, jpg, jpeg, or pdf',
            'business_registration.max' => 'Business registration must not exceed 2MB',
            'tax_document.mimes' => 'Tax document must be png, jpg, jpeg, or pdf',
            'tax_document.max' => 'Tax document must not exceed 2MB',
            'establishment_photo.mimes' => 'Establishment photo must be png, jpg, jpeg, or pdf',
            'establishment_photo.max' => 'Establishment photo must not exceed 2MB'
        ]);

        $company = Auth::guard('company')->user();

        // Scan all uploaded files for malware
        $documentFields = ['business_registration', 'tax_document', 'establishment_photo'];
        
        foreach ($documentFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                
                if (!$this->documentService->scanForMalware($file)) {
                    return back()->withErrors([
                        $field => 'File contains malicious content and cannot be uploaded'
                    ])->withInput();
                }
            }
        }

        // Store business registration document (required)
        if ($request->hasFile('business_registration')) {
            $this->documentService->storeDocument(
                $company->id,
                VerificationDocument::TYPE_BUSINESS_REGISTRATION,
                $request->file('business_registration')
            );
        }

        // Store tax document (optional)
        if ($request->hasFile('tax_document')) {
            $this->documentService->storeDocument(
                $company->id,
                VerificationDocument::TYPE_TAX_DOCUMENT,
                $request->file('tax_document')
            );
        }

        // Store establishment photo (optional)
        if ($request->hasFile('establishment_photo')) {
            $this->documentService->storeDocument(
                $company->id,
                VerificationDocument::TYPE_ESTABLISHMENT_PHOTO,
                $request->file('establishment_photo')
            );
        }

        $company->verified = null;
        $company->verified_at = null;
        $company->verification_status = 'pending';
        $company->verification_rejection_reason = null;
        $company->verification_reviewed_at = null;
        $company->save();

        return redirect()->route('company.verification.upload')
            ->with('success', __('Documents uploaded successfully! Your profile is now under review for verification.'));
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
