<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyDocumentApproval
{
    /**
     * This middleware previously blocked employers who hadn't uploaded documents.
     * Access control is now handled via the employer_trust_status tier system.
     * The middleware is kept as a passthrough to avoid breaking any route definitions.
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
