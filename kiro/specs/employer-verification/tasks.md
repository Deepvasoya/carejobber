# Implementation Plan: Employer Verification

## Overview

This implementation plan breaks down the employer verification feature into discrete coding tasks. The feature enables employers to upload verification documents (business registration, tax documents, establishment photos) with validation, secure storage, and badge management. The implementation follows Laravel best practices with a layered architecture: database migrations, models, services, controllers, and views.

## Tasks

- [x] 1. Set up database schema and migrations
  - [x] 1.1 Create verification_documents table migration
    - Create migration file with columns: id, company_id, document_type, file_data (longText), original_filename, file_size, mime_type, uploaded_at, timestamps
    - Add indexes on company_id and document_type
    - Add foreign key constraint to companies table with cascade delete
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_
  
  - [x] 1.2 Create companies table modification migration
    - Add verified_at timestamp column (nullable) after verified column
    - _Requirements: 6.3_

- [x] 2. Implement domain models
  - [x] 2.1 Create VerificationDocument model
    - Define fillable fields: company_id, document_type, file_data, original_filename, file_size, mime_type, uploaded_at
    - Add casts for uploaded_at (datetime) and file_size (integer)
    - Define document type constants: TYPE_BUSINESS_REGISTRATION, TYPE_TAX_DOCUMENT, TYPE_ESTABLISHMENT_PHOTO
    - Add company() relationship method (belongsTo)
    - Add getDecryptedFileData() method to decrypt file_data
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_
  
  - [ ]* 2.2 Write property test for document encryption round-trip
    - **Property 8: Document Encryption Round-Trip**
    - **Validates: Requirements 8.3**

  - [x] 2.3 Extend Company model with verification methods
    - Add verificationDocuments() relationship method (hasMany)
    - Add isVerified() method to check verified flag and verified_at
    - Add hasBusinessRegistration() method to check for business registration document
    - _Requirements: 6.1, 6.2, 6.3, 7.1, 7.2_

- [x] 3. Implement service layer
  - [x] 3.1 Create DocumentUploadService class
    - Define allowedMimeTypes property: image/png, image/jpeg, image/jpg, application/pdf
    - Define maxFileSize property: 2097152 bytes (2MB)
    - Implement validateFile() method to check MIME type and file size
    - Implement scanForMalware() method with ClamAV integration
    - Implement storeDocument() method to encrypt and persist documents
    - _Requirements: 2.1, 2.2, 3.1, 3.2, 3.3, 3.4, 4.3, 4.4, 4.5, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 8.1, 8.3_
  
  - [ ]* 3.2 Write property tests for file validation
    - **Property 1: File Format Validation**
    - **Property 2: File Size Validation**
    - **Validates: Requirements 2.1, 2.2, 3.1, 3.2, 3.3, 3.4**
  
  - [ ]* 3.3 Write property tests for error messaging
    - **Property 3: Invalid Format Error Messaging**
    - **Property 4: Oversized File Error Messaging**
    - **Validates: Requirements 2.3, 2.4, 3.5, 3.6**
  
  - [ ]* 3.4 Write unit tests for DocumentUploadService
    - Test validateFile() with valid and invalid formats
    - Test validateFile() with various file sizes
    - Test storeDocument() creates correct database record
    - Test malware scanning integration
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 8.1_
  
  - [x] 3.5 Create VerificationBadgeService class
    - Implement awardBadge() method to set verified=true and verified_at=now()
    - Implement revokeBadge() method to set verified=false and verified_at=null
    - Implement canDisplayBadge() method to check verified status
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 7.1, 7.2_
  
  - [ ]* 3.6 Write property test for badge award completeness
    - **Property 9: Badge Award Completeness**
    - **Validates: Requirements 6.1, 6.2, 6.3, 6.4**
  
  - [ ]* 3.7 Write unit tests for VerificationBadgeService
    - Test awardBadge() updates company record correctly
    - Test revokeBadge() clears verification status
    - Test canDisplayBadge() returns correct boolean
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 7.1, 7.2_

- [ ] 4. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Implement request validation
  - [x] 5.1 Create VerificationDocumentRequest form request class
    - Define validation rules: business_registration (required, file, mimes, max:2048), tax_document (nullable, file, mimes, max:2048), establishment_photo (nullable, file, mimes, max:2048)
    - Define custom error messages for each validation rule
    - _Requirements: 1.3, 1.4, 1.5, 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 4.1_
  
  - [ ]* 5.2 Write unit tests for VerificationDocumentRequest
    - Test validation passes with valid business registration
    - Test validation fails without business registration
    - Test validation passes with optional documents
    - Test validation fails with invalid formats
    - Test validation fails with oversized files
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 4.1_

- [x] 6. Implement controller layer
  - [x] 6.1 Create VerificationController
    - Inject DocumentUploadService in constructor
    - Implement showUploadForm() method to render upload view
    - Implement store() method to handle document uploads
    - Add malware scanning for all uploaded files
    - Return security error if malicious content detected
    - Store each document type using DocumentUploadService
    - Redirect to next profile step on success
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 8.1, 8.2_
  
  - [ ]* 6.2 Write property test for malware scanning execution
    - **Property 12: Malware Scanning Execution**
    - **Property 13: Malicious File Rejection**
    - **Validates: Requirements 8.1, 8.2**
  
  - [ ]* 6.3 Write unit tests for VerificationController
    - Test showUploadForm() renders correct view
    - Test store() with valid documents redirects successfully
    - Test store() without business registration returns error
    - Test store() with malicious file returns security error
    - Test store() with invalid format returns validation error
    - Test store() with oversized file returns validation error
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 8.1, 8.2_

- [x] 7. Implement view layer
  - [x] 7.1 Create document upload form view
    - Create resources/views/company/verification/upload.blade.php
    - Add form with POST method and multipart/form-data encoding
    - Add business registration upload section with required indicator
    - Add tax document upload section with optional indicator
    - Add establishment photo upload section with optional indicator
    - Display upload guidelines for each section
    - Display supported formats and max size for each section
    - Add file input with accept attribute for allowed formats
    - Display validation errors using @error directive
    - Add "Save and Continue" submit button
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 2.3, 2.4, 2.5, 3.5, 3.6_
  
  - [x] 7.2 Create verified badge component
    - Create resources/views/components/verified-badge.blade.php
    - Accept company parameter
    - Check if company is verified using isVerified() method
    - Display badge icon and text if verified
    - Add title attribute for accessibility
    - _Requirements: 7.1, 7.2, 7.3_
  
  - [x] 7.3 Integrate badge component into employer profile view
    - Add verified-badge component to employer profile template
    - Pass company object to component
    - _Requirements: 7.1, 7.2, 7.3_
  
  - [x] 7.4 Integrate badge component into job listings view
    - Add verified-badge component to job listing items
    - Pass job's company object to component
    - _Requirements: 7.4_
  
  - [ ]* 7.5 Write property tests for badge display
    - **Property 10: Conditional Badge Display**
    - **Property 11: Badge Display on Job Listings**
    - **Validates: Requirements 7.1, 7.2, 7.4**

- [x] 8. Implement document access control
  - [x] 8.1 Add document retrieval method to VerificationController
    - Implement show() method to retrieve and display document
    - Check authorization: requester must be document owner or admin
    - Return 403 Forbidden if unauthorized
    - Decrypt document data using getDecryptedFileData()
    - Return document with appropriate headers
    - _Requirements: 8.4, 8.5_
  
  - [ ]* 8.2 Write property test for document access authorization
    - **Property 14: Document Access Authorization**
    - **Validates: Requirements 8.4, 8.5**
  
  - [ ]* 8.3 Write unit tests for document access control
    - Test owner can access their documents
    - Test admin can access any document
    - Test unauthorized user receives 403 error
    - Test document decryption on retrieval
    - _Requirements: 8.4, 8.5_

- [x] 9. Add routes and wire components together
  - [x] 9.1 Register routes in web.php
    - Add GET route for company.verification.upload (showUploadForm)
    - Add POST route for company.verification.store (store)
    - Add GET route for company.verification.document.show (show document)
    - Apply auth:company middleware to all routes
    - _Requirements: 4.1, 4.2, 8.4_
  
  - [x] 9.2 Register services in AppServiceProvider
    - Bind DocumentUploadService to service container
    - Bind VerificationBadgeService to service container
    - _Requirements: 4.3, 4.4, 4.5, 6.1, 6.2, 6.3, 6.4_

- [ ]* 10. Write integration tests
  - [ ]* 10.1 Write end-to-end document upload test
    - Test complete flow from form submission to database storage
    - Verify all metadata is stored correctly
    - Verify file data is encrypted
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_
  
  - [ ]* 10.2 Write property test for document persistence
    - **Property 6: Document Persistence**
    - **Property 7: Complete Metadata Storage**
    - **Validates: Requirements 4.3, 4.4, 4.5, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7**
  
  - [ ]* 10.3 Write end-to-end badge award test
    - Test complete flow from badge award to profile display
    - Verify badge appears on profile
    - Verify badge appears on job listings
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 7.1, 7.2, 7.3, 7.4_

- [ ] 11. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Property tests validate universal correctness properties from the design document
- Unit tests validate specific examples and edge cases
- Malware scanning requires ClamAV or similar antivirus software installed
- Document encryption uses Laravel's built-in encrypt() and decrypt() functions
- All file uploads should be validated at both client-side (for UX) and server-side (for security)
