<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationDocument extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'document_type',
        'file_data',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer'
    ];

    /**
     * Document type constants
     */
    const TYPE_BUSINESS_REGISTRATION = 'business_registration';
    const TYPE_TAX_DOCUMENT = 'tax_document';
    const TYPE_ESTABLISHMENT_PHOTO = 'establishment_photo';

    /**
     * Get the company that owns the verification document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the decrypted file data.
     *
     * @return string
     */
    public function getDecryptedFileData(): string
    {
        return decrypt($this->file_data);
    }
}
