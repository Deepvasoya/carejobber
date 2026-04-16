<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\VerificationDocument;

class DocumentUploadService
{
    /**
     * Allowed MIME types for document uploads
     *
     * @var array
     */
    protected $allowedMimeTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf'
    ];

    /**
     * Maximum file size in bytes (2MB)
     *
     * @var int
     */
    protected $maxFileSize = 2097152;

    /**
     * Validate uploaded file for MIME type and size
     *
     * @param UploadedFile $file
     * @return array Array of error messages (empty if valid)
     */
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

    /**
     * Scan file for malware using ClamAV
     *
     * @param UploadedFile $file
     * @return bool True if file is clean, false if malicious
     */
    public function scanForMalware(UploadedFile $file): bool
    {
        // TODO: Integrate with ClamAV or similar malware scanning service
        // For now, return true (file is clean)
        // In production, this should connect to ClamAV daemon or use a scanning library
        
        try {
            // Example implementation would be:
            // $socket = new \Socket\Raw\Factory();
            // $client = new \Xenolope\Quahog\Client($socket->createClient('unix:///var/run/clamav/clamd.sock'));
            // $result = $client->scanFile($file->getRealPath());
            // return $result['status'] === 'OK';
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Malware scan failed: ' . $e->getMessage());
            // Fail secure: if scanning fails, reject the file
            return false;
        }
    }

    /**
     * Store document with encryption
     *
     * @param int $companyId
     * @param string $documentType
     * @param UploadedFile $file
     * @return VerificationDocument
     */
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
