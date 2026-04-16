@extends('layouts.app')

@push('styles')
<style>
.employer-zone {
    background:
        radial-gradient(circle at top left, rgba(33, 128, 141, 0.16), transparent 32%),
        linear-gradient(180deg, #f6fbff 0%, #ffffff 36%, #f7f8fc 100%);
    color: #17324d;
}
.employer-zone__hero {
    position: relative;
    overflow: hidden;
    padding: 84px 0 72px;
}
.employer-zone__hero:before,
.employer-zone__hero:after {
    content: "";
    position: absolute;
    border-radius: 999px;
    background: rgba(24, 104, 138, 0.08);
    z-index: 0;
}
.employer-zone__hero:before {
    width: 380px;
    height: 380px;
    top: -140px;
    right: -60px;
}
.employer-zone__hero:after {
    width: 260px;
    height: 260px;
    bottom: -90px;
    left: -70px;
}
.employer-zone__hero .container,
.employer-zone__section .container,
.employer-zone__custom .container {
    position: relative;
    z-index: 1;
}
.employer-zone__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(23, 50, 77, 0.08);
    color: #0e5f78;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.employer-zone__hero h1 {
    margin: 22px 0 18px;
    color: #12304f;
    font-size: 58px;
    line-height: 1.02;
    font-weight: 800;
    letter-spacing: -0.04em;
}
.employer-zone__hero p {
    max-width: 620px;
    margin: 0 0 28px;
    color: #4f647b;
    font-size: 18px;
    line-height: 1.75;
}
.employer-zone__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 32px;
}
.employer-zone__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 54px;
    padding: 0 24px;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.employer-zone__btn:hover,
.employer-zone__btn:focus {
    text-decoration: none;
    transform: translateY(-1px);
}
.employer-zone__btn--primary {
    background: linear-gradient(135deg, #0f7a8a 0%, #125fb3 100%);
    box-shadow: 0 16px 30px rgba(18, 95, 179, 0.22);
    color: #fff;
}
.employer-zone__btn--primary:hover,
.employer-zone__btn--primary:focus {
    color: #fff;
}
.employer-zone__btn--secondary {
    border: 1px solid rgba(18, 48, 79, 0.16);
    background: rgba(255, 255, 255, 0.9);
    color: #17324d;
}
.employer-zone__btn--secondary:hover,
.employer-zone__btn--secondary:focus {
    color: #17324d;
}
.employer-zone__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
}
.employer-zone__pill {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.86);
    box-shadow: 0 10px 24px rgba(18, 48, 79, 0.08);
    color: #35506d;
    font-size: 14px;
    font-weight: 600;
}
.employer-zone__visual {
    padding-left: 24px;
}
.employer-zone__panel {
    position: relative;
    padding: 26px;
    border-radius: 28px;
    background: linear-gradient(145deg, #14304d 0%, #1d5e7d 55%, #0d8a78 100%);
    box-shadow: 0 30px 70px rgba(19, 46, 74, 0.25);
    color: #fff;
}
.employer-zone__panel:after {
    content: "";
    position: absolute;
    inset: 18px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.employer-zone__card {
    position: relative;
    z-index: 1;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    padding: 22px;
}
.employer-zone__card + .employer-zone__card {
    margin-top: 18px;
}
.employer-zone__card--accent {
    background: #fff;
    color: #17324d;
}
.employer-zone__card h3,
.employer-zone__card h4 {
    margin: 0 0 10px;
    font-weight: 700;
}
.employer-zone__card p {
    margin: 0;
    font-size: 14px;
    line-height: 1.7;
    color: inherit;
}
.employer-zone__badge-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 16px;
}
.employer-zone__mini-badge {
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.14);
    font-size: 12px;
    font-weight: 700;
}
.employer-zone__stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-top: 18px;
}
.employer-zone__stat {
    padding: 18px 16px;
    border-radius: 18px;
    background: rgba(12, 30, 49, 0.18);
    text-align: center;
}
.employer-zone__stat strong {
    display: block;
    margin-bottom: 6px;
    font-size: 28px;
    font-weight: 800;
}
.employer-zone__stat span {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    opacity: 0.85;
}
.employer-zone__section {
    padding: 78px 0;
}
.employer-zone__section-title {
    max-width: 760px;
    margin: 0 auto 46px;
    text-align: center;
}
.employer-zone__section-title span {
    display: inline-block;
    margin-bottom: 10px;
    color: #0f7a8a;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.employer-zone__section-title h2 {
    margin: 0 0 14px;
    color: #14304d;
    font-size: 40px;
    font-weight: 800;
    letter-spacing: -0.03em;
}
.employer-zone__section-title p {
    margin: 0;
    color: #607286;
    font-size: 17px;
    line-height: 1.8;
}
.employer-zone__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
}
.employer-zone__info-card,
.employer-zone__feature-card,
.employer-zone__step-card {
    height: 100%;
    padding: 28px;
    border-radius: 22px;
    background: #fff;
    box-shadow: 0 16px 40px rgba(18, 48, 79, 0.07);
}
.employer-zone__info-card i,
.employer-zone__feature-card i {
    width: 54px;
    height: 54px;
    margin-bottom: 18px;
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(15, 122, 138, 0.12), rgba(18, 95, 179, 0.12));
    color: #125fb3;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.employer-zone__info-card h3,
.employer-zone__feature-card h3,
.employer-zone__step-card h3 {
    margin: 0 0 12px;
    color: #183551;
    font-size: 22px;
    font-weight: 700;
}
.employer-zone__info-card p,
.employer-zone__feature-card p,
.employer-zone__step-card p {
    margin: 0;
    color: #5e7186;
    line-height: 1.8;
}
.employer-zone__flow {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
}
.employer-zone__step-card {
    position: relative;
    overflow: hidden;
}
.employer-zone__step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    margin-bottom: 18px;
    border-radius: 16px;
    background: linear-gradient(135deg, #125fb3 0%, #0f7a8a 100%);
    color: #fff;
    font-size: 20px;
    font-weight: 800;
}
.employer-zone__proof {
    padding: 34px;
    border-radius: 28px;
    background: linear-gradient(140deg, #14304d 0%, #1f5b7b 100%);
    color: #fff;
}
.employer-zone__proof h2 {
    margin: 0 0 18px;
    font-size: 38px;
    font-weight: 800;
}
.employer-zone__proof p {
    margin: 0 0 26px;
    max-width: 620px;
    color: rgba(255, 255, 255, 0.84);
    line-height: 1.8;
}
.employer-zone__proof-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}
.employer-zone__proof-item {
    padding: 18px 18px 18px 54px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.08);
    position: relative;
    line-height: 1.7;
}
.employer-zone__proof-item:before {
    content: "\f00c";
    position: absolute;
    left: 18px;
    top: 19px;
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: #8af0d9;
}
.employer-zone__cta {
    padding: 0 0 82px;
}
.employer-zone__cta-box {
    padding: 42px;
    border-radius: 30px;
    background: linear-gradient(135deg, #edf9fa 0%, #eef4ff 55%, #ffffff 100%);
    box-shadow: 0 24px 60px rgba(18, 48, 79, 0.09);
    text-align: center;
}
.employer-zone__cta-box h2 {
    margin: 0 0 14px;
    color: #14304d;
    font-size: 38px;
    font-weight: 800;
}
.employer-zone__cta-box p {
    max-width: 700px;
    margin: 0 auto 26px;
    color: #5f7288;
    line-height: 1.8;
}
.employer-zone__custom {
    padding: 70px 0 88px;
}
.employer-zone__custom-body {
    padding: 34px;
    border-radius: 24px;
    background: #fff;
    box-shadow: 0 24px 60px rgba(18, 48, 79, 0.08);
}
.employer-zone__custom-body img {
    max-width: 100%;
    height: auto;
}
@media (max-width: 1199px) {
    .employer-zone__hero h1 {
        font-size: 48px;
    }
    .employer-zone__visual {
        padding-left: 0;
        margin-top: 30px;
    }
}
@media (max-width: 991px) {
    .employer-zone__hero {
        padding: 68px 0 54px;
    }
    .employer-zone__hero h1,
    .employer-zone__section-title h2,
    .employer-zone__proof h2,
    .employer-zone__cta-box h2 {
        font-size: 36px;
    }
    .employer-zone__grid,
    .employer-zone__flow,
    .employer-zone__stats,
    .employer-zone__proof-list {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 767px) {
    .employer-zone__hero h1 {
        font-size: 32px;
    }
    .employer-zone__hero p,
    .employer-zone__section-title p {
        font-size: 16px;
    }
    .employer-zone__actions {
        flex-direction: column;
        align-items: stretch;
    }
    .employer-zone__btn {
        width: 100%;
    }
    .employer-zone__panel,
    .employer-zone__proof,
    .employer-zone__cta-box,
    .employer-zone__custom-body {
        padding: 24px;
    }
}
</style>
@endpush

@section('content')
@include('includes.header')

<div class="employer-zone">
    @if($customContent)
        <section class="employer-zone__custom">
            <div class="container">
                <div class="employer-zone__custom-body">
                    {!! $customContent !!}
                </div>
            </div>
        </section>
    @else
        <section class="employer-zone__hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <span class="employer-zone__eyebrow"><i class="fas fa-briefcase"></i>{{ __('Employer Zone') }}</span>
                        <h1>{{ __('Find better candidates without sending employers into the jobseeker flow.') }}</h1>
                        <p>{{ __('Give employers a dedicated landing experience before login or registration. Present your value clearly, build trust fast, and route recruiters into the correct employer account journey.') }}</p>
                        <div class="employer-zone__actions">
                            <a href="{{ route('company.register') }}" class="employer-zone__btn employer-zone__btn--primary">{{ __('Create Employer Account') }}</a>
                            <a href="{{ route('company.login') }}" class="employer-zone__btn employer-zone__btn--secondary">{{ __('Employer Sign In') }}</a>
                        </div>
                        <div class="employer-zone__meta">
                            <span class="employer-zone__pill"><i class="fas fa-check-circle"></i>{{ __('Separate employer and candidate journey') }}</span>
                            <span class="employer-zone__pill"><i class="fas fa-shield-alt"></i>{{ __('Professional brand-first entry point') }}</span>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="employer-zone__visual">
                            <div class="employer-zone__panel">
                                <div class="employer-zone__card employer-zone__card--accent">
                                    <h3>{{ __('Built for hiring teams') }}</h3>
                                    <p>{{ __('Showcase your platform before asking recruiters to log in. Help them understand sourcing, branding, verification, and applicant management in one place.') }}</p>
                                </div>
                                <div class="employer-zone__card">
                                    <h4>{{ __('What employers get') }}</h4>
                                    <div class="employer-zone__badge-row">
                                        <span class="employer-zone__mini-badge">{{ __('Job posting') }}</span>
                                        <span class="employer-zone__mini-badge">{{ __('Resume search') }}</span>
                                        <span class="employer-zone__mini-badge">{{ __('Verified badge') }}</span>
                                    </div>
                                    <div class="employer-zone__stats">
                                        <div class="employer-zone__stat">
                                            <strong>{{ number_format($stats['active_jobs']) }}</strong>
                                            <span>{{ __('Active Jobs') }}</span>
                                        </div>
                                        <div class="employer-zone__stat">
                                            <strong>{{ number_format($stats['active_employers']) }}</strong>
                                            <span>{{ __('Employers') }}</span>
                                        </div>
                                        <div class="employer-zone__stat">
                                            <strong>{{ number_format($stats['featured_employers']) }}</strong>
                                            <span>{{ __('Featured') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="employer-zone__section">
            <div class="container">
                <div class="employer-zone__section-title">
                    <span>{{ __('Why Employers Use It') }}</span>
                    <h2>{{ __('A cleaner recruiting flow from first click to first hire.') }}</h2>
                    <p>{{ __('Instead of mixing employers into jobseeker popups, give recruiters a page that explains the offer, shows credibility, and leads to the right account actions.') }}</p>
                </div>
                <div class="employer-zone__grid">
                    <div class="employer-zone__info-card">
                        <i class="fas fa-bullhorn"></i>
                        <h3>{{ __('Post roles faster') }}</h3>
                        <p>{{ __('Move employers into a focused posting journey with a dedicated call to action and no login confusion on the homepage.') }}</p>
                    </div>
                    <div class="employer-zone__info-card">
                        <i class="fas fa-user-check"></i>
                        <h3>{{ __('Build trust early') }}</h3>
                        <p>{{ __('Use verification, company branding, and a clear value proposition before employers decide to register.') }}</p>
                    </div>
                    <div class="employer-zone__info-card">
                        <i class="fas fa-layer-group"></i>
                        <h3>{{ __('Keep journeys separate') }}</h3>
                        <p>{{ __('Homepage login and registration stay for jobseekers only, while employer actions route through a dedicated employer experience.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="employer-zone__section">
            <div class="container">
                <div class="employer-zone__section-title">
                    <span>{{ __('How It Works') }}</span>
                    <h2>{{ __('Guide employers through a simpler three-step flow.') }}</h2>
                    <p>{{ __('This keeps the homepage cleaner and gives recruiters the right expectations before they create an account.') }}</p>
                </div>
                <div class="employer-zone__flow">
                    <div class="employer-zone__step-card">
                        <div class="employer-zone__step-number">1</div>
                        <h3>{{ __('Discover the employer platform') }}</h3>
                        <p>{{ __('Recruiters click `Employers/Post Job` and land here first, instead of being thrown into a mixed login popup.') }}</p>
                    </div>
                    <div class="employer-zone__step-card">
                        <div class="employer-zone__step-number">2</div>
                        <h3>{{ __('Choose employer login or registration') }}</h3>
                        <p>{{ __('From this page they can sign in to an existing employer account or create a new employer profile.') }}</p>
                    </div>
                    <div class="employer-zone__step-card">
                        <div class="employer-zone__step-number">3</div>
                        <h3>{{ __('Start hiring with confidence') }}</h3>
                        <p>{{ __('After login they continue into employer tools such as posting jobs, managing applicants, and upgrading visibility.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="employer-zone__section">
            <div class="container">
                <div class="employer-zone__proof">
                    <h2>{{ __('Designed to feel professional before a recruiter ever logs in.') }}</h2>
                    <p>{{ __('Use this default layout as-is, or replace it with custom HTML from the admin editor. The page is built to act like a proper employer marketing entry point, not a generic auth wall.') }}</p>
                    <div class="employer-zone__proof-list">
                        <div class="employer-zone__proof-item">{{ __('Dedicated employer login and registration path separate from jobseeker auth.') }}</div>
                        <div class="employer-zone__proof-item">{{ __('Works as a better destination for `Employers/Post Job` in header and homepage flows.') }}</div>
                        <div class="employer-zone__proof-item">{{ __('Customizable through the admin HTML editor when marketing content needs updates.') }}</div>
                        <div class="employer-zone__proof-item">{{ __('Supports a branded, polished layout even when no custom CMS content is added yet.') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="employer-zone__section">
            <div class="container">
                <div class="employer-zone__section-title">
                    <span>{{ __('Employer Features') }}</span>
                    <h2>{{ __('Highlight the tools that matter to recruiters.') }}</h2>
                    <p>{{ __('You said the page does not need to match the reference exactly, so this version focuses on a clean professional presentation and clearer employer actions.') }}</p>
                </div>
                <div class="employer-zone__grid">
                    <div class="employer-zone__feature-card">
                        <i class="fas fa-file-signature"></i>
                        <h3>{{ __('Structured job posting') }}</h3>
                        <p>{{ __('Publish openings with a cleaner employer-first path that leads directly into the company account flow.') }}</p>
                    </div>
                    <div class="employer-zone__feature-card">
                        <i class="fas fa-search"></i>
                        <h3>{{ __('Candidate discovery') }}</h3>
                        <p>{{ __('Search and review talent with tools designed for recruiters instead of generic public-site prompts.') }}</p>
                    </div>
                    <div class="employer-zone__feature-card">
                        <i class="fas fa-certificate"></i>
                        <h3>{{ __('Verification and credibility') }}</h3>
                        <p>{{ __('Reinforce trust with verification messaging and employer branding before registration.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="employer-zone__cta">
            <div class="container">
                <div class="employer-zone__cta-box">
                    <h2>{{ __('Ready to give employers their own front door?') }}</h2>
                    <p>{{ __('Use this page as the destination for `Employers/Post Job`, keep the homepage login/register for jobseekers only, and maintain the content later from the admin editor without code changes.') }}</p>
                    <div class="employer-zone__actions justify-content-center">
                        <a href="{{ route('company.register') }}" class="employer-zone__btn employer-zone__btn--primary">{{ __('Register as Employer') }}</a>
                        <a href="{{ route('company.login') }}" class="employer-zone__btn employer-zone__btn--secondary">{{ __('Employer Login') }}</a>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>

@include('includes.footer')
@endsection
