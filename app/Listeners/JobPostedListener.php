<?php

namespace App\Listeners;

use Mail;
use App\Events\JobPosted;
use App\Mail\JobPostedMailable;
use App\Mail\JobPostedMailableFront;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JobPostedListener implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CompanyRegistered  $event
     * @return void
     */
    public function handle(JobPosted $event)
    {
        // Employer "pending approval" first (only when job is still inactive), then admin.
        // When auto-approved, storeFrontJob already sent JobApprovalMailable — do not send
        // a duplicate "pending" email (queued listener was arriving after approval).
        if (! (bool) $event->job->is_active) {
            Mail::send(new JobPostedMailableFront($event->job));
        }

        Mail::send(new JobPostedMailable($event->job));
    }

}
