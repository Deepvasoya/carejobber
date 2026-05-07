<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Company;
use App\Models\Medo\Employer;

class EmployerController extends Controller
{
    public function resolve(string $slug)
    {
        $employer = Employer::where('slug', $slug)->first();

        if ($employer) {
            return $this->show($employer);
        }

        $company = Company::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        return view('company.detail')->with('company', $company);
    }

    public function show(Employer $employer)
    {
        $jobs = $employer->jobs()
            ->active()
            ->with('category', 'province', 'city')
            ->orderByDesc('posted_at')
            ->get();

        return view('medo.employers.show', [
            'employer' => $employer,
            'jobs' => $jobs,
            'jobCount' => $jobs->count(),
        ]);
    }
}
