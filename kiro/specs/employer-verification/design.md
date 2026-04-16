# Design Document: Employer Verification

## Overview

The Employer Verification feature enables employers to upload verification documents (business registration, tax documents, and establishment photos) to prove their legitimacy. The system validates, stores, and encrypts these documents in the database, then awards a verified badge upon approval. This badge is publicly displayed on employer profiles and job listings to build trust with job seekers.

The feature integrates with the existing Laravel job portal application, extending the Company model and leveraging Laravel's built-in validation, storage, and encryption capabilities. The implementation follows a layered architecture with clear separation between document handling, storage, and badge management.

Key design decisions:
- Store documents as encrypted binary data in the database rather than filesystem for better security and backup consistency
- Use Laravel's native encryption for document protection
- Implement file validation at multiple layers (client-side preview, server-side validation, malware scanning)
- Separate verification status from badge display to allow for future workflow extensions

## Architecture

The system follows a layered architecture pattern:

```
┌─────────────────────────────────────────────────────────┐
│                    Presentation Layer                    │
│  (Blade Views, Form Validation, File Upload Interface)  │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                   Application Layer                      │
│     (Controllers, Request Validation, Form Requests)     │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                    Service Layer                         │
│  ┌──────────────────┐  ┌──────────────────────────┐    │
│  │ Document Upload  │  │  Verification Badge      │    │
│  │    Service       │  │      Service             │    │
│  └──────────────────┘  └──────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                     Domain Layer                         │
│  ┌──────────────────┐  ┌──────────────────────────┐    │
│  │ VerificationDoc  │  │     Company Model        │    │
│  │     Model        │  │   (Extended)             │    │
│  └──────────────────┘  └──────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                  Infrastructure Layer                    │
│        (Database, Encryption, File Validation)           │
└─────────────────────────────────────────────────────────┘
```

### Component Responsibilities

**Presentation Layer:**
- Render document upload forms with clear guidelines
- Display file format and size requirements
- Show validation errors and success messages
- Display verified badge on profiles and job listings

**Application Layer:**
- Handle HTTP requests for document uploads
- Validate file uploads using Laravel Form Requests
- Coordinate between services
- Return appropriate responses

**Service Layer:**
- `DocumentUploadService`: Validates files, scans for malware, encrypts and stores documents
- `VerificationBadgeService`: Manages badge status and visibility

**Domain Layer:**
- `VerificationDocument` model: Represents uploaded verification documents
- `Company` model: Extended with verification status and badge relationships

**Infrastructure Layer:**
- Database storage for encrypted document binaries
- Laravel encryption for document security
- ClamAV integration for malware scanning



## Components and Interfaces

### 1. VerificationDocument Model

Eloquent model representing uploaded verification documents.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationDocument extends Model
{
    protected $fillable = [
        'company_id',
        'document_type',
        'file_data',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer'
    ];

    // Document type constants
    const TYPE_BUSINESS_REGISTRATION = 'business_registration';
    const TYPE_TAX_DOCUMENT = 'tax_document';
    const TYPE_ESTABLISHMENT_PHOTO = 'establishment_photo';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getDecryptedFileData(): string
    {
        return decrypt($this->file_data);
    }
}
```

### 2. Company Model Extension

Add verification badge relationship and helper methods to existing Company model.

```php
// Add to existing Company model
public function verificationDocuments()
{
    return $this->hasMany(VerificationDocument::class);
}

public function isVerified(): bool
{
    return $this->verified === true && $this->verified_at !== null;
}

public function hasBusinessRegistration(): bool
{
    return $this->verificationDocuments()
        ->where('document_type', VerificationDocument::TYPE_BUSINESS_REGISTRATION)
        ->exists();
}
```

### 3. DocumentUploadService

Service class handling document validation, scanning, and storage.

```php
namespace App\Services;

class DocumentUploadService
{
    protected $allowedMimeTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf'
    ];

    protected $maxFileSize = 2097152; // 2MB in bytes

    public function validateFile(UploadedFile $file): array
    {
        $errors = [];

        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            $errors[] = 'File must be png, jpg, jpeg, or pdf';
        }

        if ($file->getSize() > $this->maxFileSize) {
            $errors[] = 'File size must not exceed 2MB';
        }

        return $errors;
    }

    public function scanForMalware(UploadedFile $file): bool
    {
        // Integration with ClamAV or similar
        // Returns true if file is clean, false if malicious
    }

    public function storeDocument(
        int $companyId,
        string $documentType,
        UploadedFile $file
    ): VerificationDocument
    {
        $fileData = file_get_contents($file->getRealPath());
        $encryptedData = encrypt($fileData);

        return VerificationDocument::create([
            'company_id' => $companyId,
            'document_type' => $documentType,
            'file_data' => $encryptedData,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_at' => now()
        ]);
    }
}
```

### 4. VerificationBadgeService

Service class managing verification badge status.

```php
namespace App\Services;

class VerificationBadgeService
{
    public function awardBadge(Company $company): void
    {
        $company->update([
            'verified' => true,
            'verified_at' => now()
        ]);
    }

    public function revokeBadge(Company $company): void
    {
        $company->update([
            'verified' => false,
            'verified_at' => null
        ]);
    }

    public function canDisplayBadge(Company $company): bool
    {
        return $company->verified === true;
    }
}
```

### 5. VerificationDocumentRequest

Form Request for validating document uploads.

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificationDocumentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'business_registration' => 'required|file|mimes:png,jpg,jpeg,pdf|max:2048',
            'tax_document' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
            'establishment_photo' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'business_registration.required' => 'Business registration document is required',
            'business_registration.mimes' => 'Business registration must be png, jpg, jpeg, or pdf',
            'business_registration.max' => 'Business registration must not exceed 2MB',
            'tax_document.mimes' => 'Tax document must be png, jpg, jpeg, or pdf',
            'tax_document.max' => 'Tax document must not exceed 2MB',
            'establishment_photo.mimes' => 'Establishment photo must be png, jpg, jpeg, or pdf',
            'establishment_photo.max' => 'Establishment photo must not exceed 2MB'
        ];
    }
}
```

### 6. VerificationController

Controller handling document upload requests.

```php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerificationDocumentRequest;
use App\Services\DocumentUploadService;

class VerificationController extends Controller
{
    protected $documentService;

    public function __construct(DocumentUploadService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function showUploadForm()
    {
        return view('company.verification.upload');
    }

    public function store(VerificationDocumentRequest $request)
    {
        $company = auth()->guard('company')->user();

        // Scan for malware
        foreach (['business_registration', 'tax_document', 'establishment_photo'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                
                if (!$this->documentService->scanForMalware($file)) {
                    return back()->withErrors([
                        $field => 'File contains malicious content and cannot be uploaded'
                    ]);
                }
            }
        }

        // Store documents
        if ($request->hasFile('business_registration')) {
            $this->documentService->storeDocument(
                $company->id,
                VerificationDocument::TYPE_BUSINESS_REGISTRATION,
                $request->file('business_registration')
            );
        }

        if ($request->hasFile('tax_document')) {
            $this->documentService->storeDocument(
                $company->id,
                VerificationDocument::TYPE_TAX_DOCUMENT,
                $request->file('tax_document')
            );
        }

        if ($request->hasFile('establishment_photo')) {
            $this->documentService->storeDocument(
                $company->id,
                VerificationDocument::TYPE_ESTABLISHMENT_PHOTO,
                $request->file('establishment_photo')
            );
        }

        return redirect()->route('company.profile.next-step')
            ->with('success', 'Documents uploaded successfully');
    }
}
```

### 7. View Components

**Upload Form Blade Template:**

```blade
<!-- resources/views/company/verification/upload.blade.php -->
<form method="POST" action="{{ route('company.verification.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="upload-section">
        <h3>Business Registration Document <span class="required">*</span></h3>
        <p class="guidelines">Please ensure documents are clear, readable, well-lit, and in focus</p>
        <p class="format-info">Supported formats: PNG, JPG, JPEG, PDF | Max size: 2MB</p>
        
        <input type="file" name="business_registration" accept=".png,.jpg,.jpeg,.pdf" required>
        @error('business_registration')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <div class="upload-section">
        <h3>Tax Document <span class="optional">(Optional)</span></h3>
        <p class="guidelines">Please ensure documents are clear, readable, well-lit, and in focus</p>
        <p class="format-info">Supported formats: PNG, JPG, JPEG, PDF | Max size: 2MB</p>
        
        <input type="file" name="tax_document" accept=".png,.jpg,.jpeg,.pdf">
        @error('tax_document')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <div class="upload-section">
        <h3>Establishment Photo <span class="optional">(Optional)</span></h3>
        <p class="guidelines">Please ensure documents are clear, readable, well-lit, and in focus</p>
        <p class="format-info">Supported formats: PNG, JPG, JPEG, PDF | Max size: 2MB</p>
        
        <input type="file" name="establishment_photo" accept=".png,.jpg,.jpeg,.pdf">
        @error('establishment_photo')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Save and Continue</button>
</form>
```

**Badge Display Component:**

```blade
<!-- resources/views/components/verified-badge.blade.php -->
@if($company->isVerified())
    <span class="verified-badge" title="Verified Employer">
        <i class="fa fa-check-circle"></i> Verified
    </span>
@endif
```



## Data Models

### Database Schema

**verification_documents table:**

```sql
CREATE TABLE verification_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id INT UNSIGNED NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    file_data LONGBLOB NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_company_id (company_id),
    INDEX idx_document_type (document_type),
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);
```

**companies table modifications:**

```sql
ALTER TABLE companies 
ADD COLUMN verified_at TIMESTAMP NULL AFTER verified;
```

Note: The `verified` boolean column already exists in the companies table.

### Migration Files

**Create verification_documents table:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('document_type', 50);
            $table->longText('file_data'); // Encrypted binary data stored as base64
            $table->string('original_filename', 255);
            $table->unsignedInteger('file_size');
            $table->string('mime_type', 100);
            $table->timestamp('uploaded_at');
            $table->timestamps();

            $table->index('company_id');
            $table->index('document_type');
            
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_documents');
    }
};
```

**Add verified_at to companies table:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('verified');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('verified_at');
        });
    }
};
```

### Data Flow

**Document Upload Flow:**

```
User selects file → Client-side preview → Form submission → 
Request validation → Malware scan → Encryption → 
Database storage → Success response
```

**Badge Award Flow:**

```
Admin reviews documents → Approves verification → 
Badge service awards badge → Update company.verified = true → 
Update company.verified_at = now() → Badge visible on profile
```

**Badge Display Flow:**

```
User views profile → Check company.verified → 
If true, render badge component → Display verified icon
```



## Correctness Properties

A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.

### Property 1: File Format Validation

For any document upload (business registration, tax document, or establishment photo), the system should accept only files with MIME types of image/png, image/jpeg, image/jpg, or application/pdf, and reject all other formats.

**Validates: Requirements 2.1, 3.1, 3.3**

### Property 2: File Size Validation

For any document upload, the system should accept only files with size ≤ 2MB (2,097,152 bytes) and reject files exceeding this limit.

**Validates: Requirements 2.2, 3.2, 3.4**

### Property 3: Invalid Format Error Messaging

For any file upload with an invalid MIME type, the validation should return an error message indicating the supported formats (png, jpg, jpeg, pdf).

**Validates: Requirements 2.3, 3.5**

### Property 4: Oversized File Error Messaging

For any file upload exceeding 2MB, the validation should return an error message indicating the maximum size limit.

**Validates: Requirements 2.4, 3.6**

### Property 5: Valid File Confirmation

For any valid file upload (correct format and size), the system should provide confirmation feedback (preview or filename display).

**Validates: Requirements 2.5**

### Property 6: Document Persistence

For any valid document uploaded and submitted, the document should be retrievable from the database with the correct association to the employer's company ID.

**Validates: Requirements 4.3, 4.4, 4.5**

### Property 7: Complete Metadata Storage

For any document successfully stored, the database record should contain all required metadata: file binary data, original filename, file size, MIME type, upload timestamp, company ID, and document type category.

**Validates: Requirements 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7**

### Property 8: Document Encryption Round-Trip

For any document stored in the database, encrypting the file data and then decrypting it should yield the original file binary data (round-trip property).

**Validates: Requirements 8.3**

### Property 9: Badge Award Completeness

For any employer approved for verification, the badge award operation should set both the verified flag to true and the verified_at timestamp to the current time, and these values should be persisted and retrievable from the database.

**Validates: Requirements 6.1, 6.2, 6.3, 6.4**

### Property 10: Conditional Badge Display

For any employer profile, the verified badge should be displayed if and only if the employer's verified flag is true.

**Validates: Requirements 7.1, 7.2**

### Property 11: Badge Display on Job Listings

For any job listing, if the associated employer has verified=true, then the verified badge should be displayed alongside the job posting.

**Validates: Requirements 7.4**

### Property 12: Malware Scanning Execution

For any file upload, the file should be passed through malware scanning before storage operations are performed.

**Validates: Requirements 8.1**

### Property 13: Malicious File Rejection

For any file flagged as malicious by the malware scanner, the upload should be rejected and a security error message should be returned to the user.

**Validates: Requirements 8.2**

### Property 14: Document Access Authorization

For any document retrieval request, the system should grant access if and only if the requester is either the document owner (company that uploaded it) or an authorized administrator.

**Validates: Requirements 8.4, 8.5**



## Error Handling

### Validation Errors

**File Format Errors:**
- Error Code: `INVALID_FILE_FORMAT`
- Message: "File must be png, jpg, jpeg, or pdf"
- HTTP Status: 422 Unprocessable Entity
- Handling: Return to upload form with error message, preserve other form data

**File Size Errors:**
- Error Code: `FILE_TOO_LARGE`
- Message: "File size must not exceed 2MB"
- HTTP Status: 422 Unprocessable Entity
- Handling: Return to upload form with error message, preserve other form data

**Missing Required Document:**
- Error Code: `REQUIRED_DOCUMENT_MISSING`
- Message: "Business registration document is required"
- HTTP Status: 422 Unprocessable Entity
- Handling: Return to upload form with error message highlighting the missing field

### Security Errors

**Malicious Content Detected:**
- Error Code: `MALICIOUS_CONTENT_DETECTED`
- Message: "File contains malicious content and cannot be uploaded"
- HTTP Status: 403 Forbidden
- Handling: Reject upload immediately, log security event, notify administrators
- Logging: Include file hash, upload timestamp, company ID, scanner result

**Unauthorized Access:**
- Error Code: `UNAUTHORIZED_DOCUMENT_ACCESS`
- Message: "You do not have permission to access this document"
- HTTP Status: 403 Forbidden
- Handling: Deny access, log access attempt with requester details

### Storage Errors

**Database Write Failure:**
- Error Code: `DOCUMENT_STORAGE_FAILED`
- Message: "Unable to save document. Please try again."
- HTTP Status: 500 Internal Server Error
- Handling: Rollback transaction, log error details, display user-friendly message
- Recovery: Implement retry logic with exponential backoff

**Encryption Failure:**
- Error Code: `ENCRYPTION_FAILED`
- Message: "Unable to secure document. Please try again."
- HTTP Status: 500 Internal Server Error
- Handling: Do not store unencrypted data, log error, notify administrators
- Recovery: Check encryption key configuration, retry operation

**Decryption Failure:**
- Error Code: `DECRYPTION_FAILED`
- Message: "Unable to retrieve document"
- HTTP Status: 500 Internal Server Error
- Handling: Log error with document ID, notify administrators
- Recovery: Verify encryption key hasn't changed, check data integrity

### System Errors

**Malware Scanner Unavailable:**
- Error Code: `SCANNER_UNAVAILABLE`
- Message: "Document verification service temporarily unavailable. Please try again later."
- HTTP Status: 503 Service Unavailable
- Handling: Reject upload (fail secure), log service outage, alert administrators
- Recovery: Queue document for scanning when service recovers, or implement fallback scanner

**File System Errors:**
- Error Code: `FILE_READ_ERROR`
- Message: "Unable to process uploaded file"
- HTTP Status: 500 Internal Server Error
- Handling: Clean up temporary files, log error, display user-friendly message

### Error Logging Strategy

All errors should be logged with the following information:
- Timestamp
- Error code and message
- Company ID (if authenticated)
- Request details (IP address, user agent)
- Stack trace (for system errors)
- Context data (file size, MIME type, document type)

Security-related errors should trigger additional monitoring alerts.



## Testing Strategy

### Dual Testing Approach

The testing strategy employs both unit tests and property-based tests to ensure comprehensive coverage:

- **Unit tests** verify specific examples, edge cases, and integration points
- **Property-based tests** verify universal properties across all inputs through randomization
- Both approaches are complementary and necessary for complete validation

### Property-Based Testing

**Framework:** PHPUnit with [Eris](https://github.com/giorgiosironi/eris) for property-based testing in PHP

**Configuration:**
- Minimum 100 iterations per property test
- Each test tagged with reference to design document property
- Tag format: `@group Feature: employer-verification, Property {number}: {property_text}`

**Property Test Coverage:**

1. **File Format Validation Property Test**
   - Generate random files with various MIME types
   - Verify only allowed types (png, jpg, jpeg, pdf) pass validation
   - Tag: `@group Feature: employer-verification, Property 1: File Format Validation`

2. **File Size Validation Property Test**
   - Generate random files with sizes from 0 bytes to 5MB
   - Verify only files ≤ 2MB pass validation
   - Tag: `@group Feature: employer-verification, Property 2: File Size Validation`

3. **Invalid Format Error Property Test**
   - Generate random invalid MIME types
   - Verify all return appropriate error message
   - Tag: `@group Feature: employer-verification, Property 3: Invalid Format Error Messaging`

4. **Oversized File Error Property Test**
   - Generate random files > 2MB
   - Verify all return size limit error message
   - Tag: `@group Feature: employer-verification, Property 4: Oversized File Error Messaging`

5. **Document Persistence Property Test**
   - Generate random valid documents with various types
   - Upload and verify retrieval from database
   - Tag: `@group Feature: employer-verification, Property 6: Document Persistence`

6. **Metadata Storage Property Test**
   - Generate random documents with various metadata
   - Verify all metadata fields are stored correctly
   - Tag: `@group Feature: employer-verification, Property 7: Complete Metadata Storage`

7. **Encryption Round-Trip Property Test**
   - Generate random binary data
   - Verify encrypt(data) then decrypt yields original data
   - Tag: `@group Feature: employer-verification, Property 8: Document Encryption Round-Trip`

8. **Badge Award Property Test**
   - Generate random company records
   - Award badge and verify both verified flag and timestamp are set
   - Tag: `@group Feature: employer-verification, Property 9: Badge Award Completeness`

9. **Conditional Badge Display Property Test**
   - Generate random companies with various verified states
   - Verify badge displays if and only if verified=true
   - Tag: `@group Feature: employer-verification, Property 10: Conditional Badge Display`

10. **Badge on Job Listings Property Test**
    - Generate random jobs with verified/unverified employers
    - Verify badge appears only for verified employers
    - Tag: `@group Feature: employer-verification, Property 11: Badge Display on Job Listings`

11. **Document Access Authorization Property Test**
    - Generate random access requests from various user types
    - Verify access granted only to owner or admin
    - Tag: `@group Feature: employer-verification, Property 14: Document Access Authorization`

### Unit Testing

**Framework:** PHPUnit

**Unit Test Coverage:**

**Controller Tests:**
- Test upload form renders with correct structure
- Test successful document submission redirects correctly
- Test validation errors return to form with messages
- Test malware detection rejects upload
- Test unauthorized access is denied

**Service Tests:**
- `DocumentUploadService::validateFile()` with specific valid/invalid examples
- `DocumentUploadService::storeDocument()` creates correct database record
- `VerificationBadgeService::awardBadge()` updates company record
- `VerificationBadgeService::canDisplayBadge()` returns correct boolean

**Model Tests:**
- `VerificationDocument::getDecryptedFileData()` decrypts correctly
- `Company::isVerified()` returns correct status
- `Company::hasBusinessRegistration()` checks document existence

**Integration Tests:**
- Complete upload flow from form submission to database storage
- Badge award flow from approval to profile display
- Document retrieval with authorization checks

**Edge Cases:**
- Empty file upload
- Corrupted file data
- Concurrent uploads from same company
- Badge award for already-verified company
- Document access after company deletion

**Example Unit Test:**

```php
class DocumentUploadServiceTest extends TestCase
{
    /** @test */
    public function it_rejects_invalid_file_formats()
    {
        $service = new DocumentUploadService();
        $file = UploadedFile::fake()->create('document.txt', 100);
        
        $errors = $service->validateFile($file);
        
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('png, jpg, jpeg, or pdf', $errors[0]);
    }
    
    /** @test */
    public function it_rejects_oversized_files()
    {
        $service = new DocumentUploadService();
        $file = UploadedFile::fake()->create('document.pdf', 3000); // 3MB
        
        $errors = $service->validateFile($file);
        
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('2MB', $errors[0]);
    }
}
```

**Example Property Test:**

```php
use Eris\Generator;

class DocumentEncryptionPropertyTest extends TestCase
{
    use Eris\TestTrait;

    /**
     * @test
     * @group Feature: employer-verification, Property 8: Document Encryption Round-Trip
     */
    public function encryption_round_trip_preserves_data()
    {
        $this->forAll(
            Generator\string()
        )
        ->withMaxSize(100)
        ->then(function ($originalData) {
            $encrypted = encrypt($originalData);
            $decrypted = decrypt($encrypted);
            
            $this->assertEquals($originalData, $decrypted);
        });
    }
}
```

### Test Execution

**Local Development:**
```bash
# Run all tests
php artisan test

# Run only property tests
php artisan test --group=employer-verification

# Run specific property test
php artisan test --filter=encryption_round_trip_preserves_data
```

**CI/CD Pipeline:**
- All tests must pass before deployment
- Property tests run with 100 iterations minimum
- Code coverage target: 90% for new code
- Security tests (malware scanning, encryption) are mandatory

### Manual Testing Checklist

- [ ] Upload valid business registration document
- [ ] Upload all three document types
- [ ] Try uploading invalid file format
- [ ] Try uploading oversized file
- [ ] Submit without required document
- [ ] Verify badge appears after approval
- [ ] Verify badge visible on profile
- [ ] Verify badge visible on job listings
- [ ] Verify non-verified employers don't show badge
- [ ] Test document access as owner
- [ ] Test document access as admin
- [ ] Test document access as unauthorized user

