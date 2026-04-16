<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create CMS page entry for employer landing page
        $cmsId = DB::table('cms')->insertGetId([
            'page_slug' => 'employer-landing-page',
            'show_in_top_menu' => false,
            'show_in_footer_menu' => false,
            'seo_title' => 'Employer Zone - Post Jobs & Hire Talent',
            'seo_description' => 'Join our employer platform to post jobs, access qualified candidates, and grow your team.',
            'seo_keywords' => 'employer, post job, hire, recruitment',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create default content for the employer landing page
        DB::table('cms_content')->insert([
            'page_id' => $cmsId,
            'page_title' => 'Employer Zone',
            'page_content' => $this->getDefaultContent(),
            'lang' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the CMS content and page entry
        $cms = DB::table('cms')->where('page_slug', 'employer-landing-page')->first();
        
        if ($cms) {
            DB::table('cms_content')->where('page_id', $cms->id)->delete();
            DB::table('cms')->where('id', $cms->id)->delete();
        }
    }

    /**
     * Get default HTML content for employer landing page
     */
    private function getDefaultContent(): string
    {
        return <<<'HTML'
<div class="employer-landing-page">
    <!-- Hero Section -->
    <section class="hero-section text-center py-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Hire Globally, Faster and Smarter</h1>
            <p class="lead mb-4">Connect with qualified candidates and build your dream team with our powerful recruitment platform</p>
            <div class="cta-buttons">
                <a href="/company/register" class="btn btn-primary btn-lg me-3">Get Started</a>
                <a href="/company/login" class="btn btn-outline-primary btn-lg">Sign In</a>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our Platform?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="benefit-card text-center p-4">
                        <div class="icon mb-3">
                            <i class="fas fa-clock fa-3x text-primary"></i>
                        </div>
                        <h3 class="h5">Faster Hiring Process</h3>
                        <p>Streamline your recruitment with our efficient tools and reach qualified candidates quickly</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card text-center p-4">
                        <div class="icon mb-3">
                            <i class="fas fa-users fa-3x text-primary"></i>
                        </div>
                        <h3 class="h5">Access More Applicants</h3>
                        <p>Tap into our extensive network of job seekers actively looking for opportunities</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card text-center p-4">
                        <div class="icon mb-3">
                            <i class="fas fa-certificate fa-3x text-primary"></i>
                        </div>
                        <h3 class="h5">Verified Employer Badge</h3>
                        <p>Build trust with candidates by displaying your verified employer status</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">How It Works</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step-card text-center">
                        <div class="step-number mb-3">
                            <span class="badge bg-primary rounded-circle p-3 fs-4">1</span>
                        </div>
                        <h3 class="h5">Create Company Profile</h3>
                        <p>Set up your company profile and showcase your organization to potential candidates</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card text-center">
                        <div class="step-number mb-3">
                            <span class="badge bg-primary rounded-circle p-3 fs-4">2</span>
                        </div>
                        <h3 class="h5">Post a Job</h3>
                        <p>Create detailed job listings with our easy-to-use posting tools</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card text-center">
                        <div class="step-number mb-3">
                            <span class="badge bg-primary rounded-circle p-3 fs-4">3</span>
                        </div>
                        <h3 class="h5">Hire Job Seekers</h3>
                        <p>Review applications, connect with candidates, and make your hiring decisions</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Powerful Features for Employers</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-item">
                        <h4><i class="fas fa-search text-primary me-2"></i>Streamlined Sourcing</h4>
                        <p>Find the right candidates with advanced search and filtering capabilities</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <h4><i class="fas fa-bolt text-primary me-2"></i>Instant Onboarding</h4>
                        <p>Get started quickly with our intuitive platform and easy setup process</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <h4><i class="fas fa-chart-line text-primary me-2"></i>Seamless Management</h4>
                        <p>Manage all your job postings and applications from one central dashboard</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section py-5 text-center">
        <div class="container">
            <h2 class="mb-4">Ready to Find Your Next Great Hire?</h2>
            <p class="lead mb-4">Join thousands of employers who trust our platform for their recruitment needs</p>
            <div class="cta-buttons">
                <a href="/company/register" class="btn btn-primary btn-lg me-3">Create Free Account</a>
                <a href="/company/login" class="btn btn-outline-primary btn-lg">Sign In</a>
            </div>
        </div>
    </section>
</div>
HTML;
    }
};
