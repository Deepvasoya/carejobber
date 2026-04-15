<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentsUpload extends Mailable
{

    use Queueable,
        SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       if($this->data['is_admin']){
           $recipientAddress = $this->data['email'];
           $recipientName = $this->data['full_name'];
           
           // Determine which template to use based on status
           if($this->data['company']->is_active == 1){
                $templateSlug = 'document-account-approved';
           }elseif($this->data['status'] !=1){
                $templateSlug = 'document-resubmit-request';
           }else{
                $templateSlug = 'document-pending-company';
           }
          
       }else{
           $recipientAddress = config('mail.recieve_to.address');
           $recipientName = config('mail.recieve_to.name');
           $templateSlug = 'document-pending-admin';
       }

       // Prepare data for template
       $templateData = [
           'FULL_NAME' => $this->data['full_name'],
           'COMPANY_NAME' => $this->data['company']->name,
           'COMPANY_LINK' => route('company.detail', $this->data['company']->slug),
           'COMPANY_ADMIN_LINK' => route('edit.company', ['id' => $this->data['company']->id])
       ];

       // Get parsed template
       $parsed = \App\Services\EmailTemplateService::parseTemplate($templateSlug, $templateData);

       if (!$parsed) {
           // Fallback to old method if template not found
           if($this->data['is_admin']){
               if($this->data['company']->is_active == 1){
                    $subject = 'Account Approved Start Posting Jobs';
               }elseif($this->data['status'] !=1){
                    $subject = 'Resubmit Job Posting Request Declined';
               }else{
                    $subject = 'Registration Verification in Progress';
               }
           }else{
               $subject = 'New Employer Registration Approval Required';
           }

           return $this->from([
            'address' => config('mail.recieve_to.address'),
            'name' => config('mail.recieve_to.name'),
        ])
        ->to($recipientAddress, $recipientName)
        ->replyTo($this->data['email'], $this->data['full_name'])
        ->subject($subject)
        ->view('emails.send_document_message')
        ->with($this->data);
       }
        
       return $this->from([
        'address' => config('mail.recieve_to.address'),
        'name' => config('mail.recieve_to.name'),
    ])
    ->to($recipientAddress, $recipientName)
    ->replyTo($this->data['email'], $this->data['full_name'])
    ->subject($parsed['subject'])
    ->html($parsed['body']);
    }

}
