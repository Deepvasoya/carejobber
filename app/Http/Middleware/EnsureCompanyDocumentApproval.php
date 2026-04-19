<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyDocumentApproval
{
    /**
     * Ensure the logged-in company has uploaded required documents and has been approved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $company = Auth::guard('company')->user();

        if (! $company) {
            return redirect()->route('company.login');
        }

        if (! $company->hasBusinessRegistration()) {
            return redirect()
                ->route('company.verification.upload')
                ->with('info', __('Upload your business registration document first. Admin approval is required before you can post jobs or view candidate resumes.'));
        }

        if ($company->isVerificationRejected()) {
            return redirect()
                ->route('company.verification.upload')
                ->with('error', __('Your business documents were rejected. Please review the reason, upload corrected documents, and wait for admin approval before posting jobs or viewing resumes.'));
        }

        if ((int) $company->is_active !== 1) {
            return redirect()
                ->route('company.verification.upload')
                ->with('info', __('Your employer account is pending admin approval. You can post jobs and view candidate resumes after approval.'));
        }

        if (! $company->isVerified()) {
            return redirect()
                ->route('company.verification.upload')
                ->with('info', __('Your business documents are under review. You can post jobs and view candidate resumes after admin approval.'));
        }

        return $next($request);
    }
}
