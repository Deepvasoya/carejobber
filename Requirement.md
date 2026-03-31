First Red My Full DB
username : root
password : Deepvasoya

Prompt 1: Full Overhaul Prompt (Best to Start With – Broad but Guided)


You are an expert Laravel developer (Laravel 10/11 preferred). I have an existing Laravel job board website at https://yobist.com (healthcare-focused, Canada-based, with job seeker and employer dashboards, resume builder, job posting, basic search).

I need to heavily customize core functionality. Implement the following changes step-by-step with clean, modern Laravel code (use best practices: Eloquent relationships, Policies/Gates, Livewire or Blade components where dynamic, Laravel Cashier for payments if needed, migrations, seeders if required, etc.). Provide full code files/structure changes, explain what to add/modify, and suggest any new packages (e.g., Laravel Cashier, Spatie permissions if needed).

Key requirements in priority order:

1. Subscriptions & Packages:
   - Employers: Monthly recurring subscriptions + pay-per-post packages (credits for posting jobs).
   - Job seekers: Pay to promote/boost their resumes (e.g., featured for X days).
   - Integrate Stripe via Laravel Cashier. Create Package model (name, price, type: recurring/credits, posts/visibility allowed, etc.). Handle checkout, webhooks, access gating.

2. Partial Resume View for Employers:
   - Employers see only partial resume: job history + education (stored in JSON or related tables).
   - No name, email, phone, or full details until they pay a one-time unlock fee per resume (e.g., $5–10 via Stripe).
   - Track unlocks per employer-resume. Show "Unlock Full Profile" button that triggers payment.

3. Resume Listing:
   - In employer search/dashboard: show resumes in card/list format displaying only partial info (history, education, headline, location if allowed).
   - Paginated, searchable.

4. Custom Job Application Flow:
   - When job seeker clicks "Apply", open a modal/pop-up (use Livewire or Alpine + modal).
   - Options: 
     - Select existing resume from account (dropdown).
     - OR upload new resume file (PDF/doc, store in storage).
   - Textarea for cover letter (paste or type, max 2000 chars).
   - Submit creates Application record with resume path/reference + cover letter.

5. Secure Admin URL:
   - Hide /admin behind a random/obfuscated prefix (e.g., /x7k9p2m-admin-panel-abc123).
   - Set via .env (ADMIN_PREFIX=secret-panel-xyz).
   - Custom middleware to check prefix or 404. Protect all admin routes.

6. Dynamic Multi-Level Locations:
   - Admin setting (1, 2, or 3 levels) stored in settings table.
   - Level 1: only cities shown everywhere (jobs, search, profiles).
   - Level 2: state > city (cascading dropdowns).
   - Level 3: country > state > city.
   - Use separate Country/State/City models with relationships.
   - Update all forms/search/filters dynamically (use JS cascading selects, e.g., Select2 or native + Livewire).

7. Homepage Search Form Change:
   - Current: probably job title + category.
   - Change to: Job title/keywords + location (multi-level aware).
   - Update controller query to filter by title + location.

Start by suggesting the new models/migrations needed, then provide code for #7 (easiest/quick win), then #5, then move to payments (#1), etc. Assume standard Laravel auth (maybe Jetstream or Breeze). Use Blade + Livewire for UI where dynamic. Provide route, controller, model, migration, blade/view, and policy/gate examples.

If anything is unclear from the live site, assume typical job board structure (jobs, users [role: seeker/employer], resumes, applications, categories, locations as string or relation).

Output in markdown with code blocks, file paths, and step-by-step instructions.


Prompt 2: Focused on Subscriptions & Payments (High Priority Feature)

Expert Laravel + Stripe developer needed.

For my Laravel job board[](https://yobist.com), implement a flexible subscription & package system.

Requirements:
- Packages table: id, name, price (decimal), type (monthly_recurring / one_time_credits / resume_boost), credits_or_posts (int), duration_days (for boosts), stripe_price_id (nullable), is_active (bool).
- Employers buy packages → get credits for job posts or monthly unlimited (based on type).
- Job seekers buy resume promotion packages → resume gets featured/highlighted for X days.
- Use Laravel Cashier (Stripe) for recurring subs and one-time payments.
- Dashboard sections: show active subscription, remaining credits, buy more buttons.
- Gates: canPostJob() checks credits or active sub.
- Webhook handling for subscription updates/cancellations.

Provide:
- Migration & Model for Package, Subscription (extend Cashier if needed), CreditTransaction.
- Controller + routes for purchase/checkout (employer & seeker separate).
- Blade/Livewire components for package listing & checkout.
- Example policy/gate usage.
- Seeder with sample packages (e.g., Basic Monthly $29 → unlimited posts, Pay-per-Post $10/credit).

Use modern Laravel (10/11), Cashier v15+, Stripe checkout sessions.

Prompt 3: Focused on Partial Resumes + Unlock Payment

Laravel expert: Implement paid resume unlock feature for employers.

Context: Job board where job seekers have resumes. Employers see search/list of resumes but only partial view (job history + education) for free.

Requirements:
- Resume model has partial_data JSON column { "history": [...], "education": [...] } or separate relations.
- Employers see list/cards with partial info only.
- "View Full Profile" button → if not unlocked, show Stripe paywall ($X one-time).
- After payment success → create Unlock record (user_id employer, resume_id, paid_at).
- Unlocked → show full name, email, phone, full resume PDF/link, etc.
- Prevent direct access to full details without unlock.

Provide:
- Migration for unlocks table.
- Resume model updates (casts, scopes, isUnlockedBy method).
- Gate/Policy: view-full-resume.
- Livewire component or controller for unlock + Stripe checkout.
- Blade snippets for partial vs full view.
- Webhook if needed for payment confirmation.

Use Cashier for one-time charge.

Prompt 4: Quick Wins (Homepage + Admin URL + Locations)

Implement these quick customizations in my Laravel job board:

1. Change homepage search form from [title + category] to [job title/keywords + location].
   - Update welcome.blade.php form.
   - Modify SearchController or JobController query (Eloquent whereLike title + location filter).
   - Make location dropdown dynamic if multi-level later.

2. Secure admin panel with custom URL prefix.
   - .env: ADMIN_PREFIX=super-secret-9f8a2
   - Middleware: CheckAdminPrefix → abort(404) if not matching prefix.
   - Update route group: Route::prefix(env('ADMIN_PREFIX'))->middleware('admin.prefix')...

3. Prepare for multi-level locations (admin toggle 1/2/3 levels).
   - Create Country, Province/State, City models + migrations + relations.
   - Settings table: location_levels (int 1-3).
   - Helper function or config to return appropriate location collection based on level.

Provide full code diffs/additions: migrations, models, middleware, routes, blade form update, controller changes.


You are a senior Laravel + Tailwind + Livewire expert building a custom healthcare job board (Canada-focused, like CareJobber.com).

My current site is https://yobist.com (Laravel-based). I want to overhaul the employer/recruiter side, especially packages/subscriptions, resume database access, and job application flow.

Implement these features with modern, clean code (Laravel 10/11, Livewire for dynamic parts, Tailwind CSS for styling, Stripe via Laravel Cashier for payments). Match the UI style from my screenshots as closely as possible:

1. Resume search / listing page (employer side):
   - Shows list/cards like: "No Name • Beaumont, AB" (anonymous by default)
   - Partial info only: Relevant Work Experience (bullet points), Education (e.g. "Diploma, NorQuest College"), Licences/Certifications
   - Big blue "View all details" button with lock icon (padlock) → only visible if not unlocked
   - When clicked → if not paid/unlocked → trigger Stripe one-time payment popup/modal to unlock full profile (name, email, phone, full resume)
   - After payment → show full details + save unlock record (so future views are free for that employer)
   - Filters sidebar: checkboxes for categories (Hospital experience, Senior care, etc.), Job titles (LPN, Registered Nurse, etc.)
   - Top: "1,300 resumes match your criteria" + sorting (Relevance ↓)

2. Packages & Subscriptions page (/recruiter/posting/packages or similar):
   - Hero section: SVG laptop icon + "Packages and Subscriptions" heading + "Simple pricing. No surprise fees. Advanced features."
   - Tabs: "Packages" (active by default) | "Subscriptions"
   - Under Packages tab:
     - Bullet list: "Easy and instant posting process..." + "Job posting credits never expire..."
     - Country selector (flag icon + dropdown, default Canada, "Change country" link, help tooltip modal explaining credits per country)
     - "Select your package" heading
     - Radio cards (selectable with blue circle):
       - e.g. "3 job postings" + "30% rebate" badge (green check) + price (e.g. $273.00 CAD)
       - 5 postings + 40% rebate + higher price
       - 10 postings + 50% rebate
     - Each card has "Buy now →" blue button + payment icons (Visa, MC, Amex, PayPal gray)
   - Subscriptions tab: similar cards for time-based (3 months unlimited $1,290, 6 months $1,990, 12 months $2,590) + "Unlimited job postings" badge
   - Use radio inputs for selection, show selected card highlighted ("on" class)
   - After select → redirect to billing/checkout (Stripe session)

3. Other required customizations:
   - Job seeker resume promotion: separate packages (e.g. "Featured Resume – 30 days" pay-once)
   - Partial resumes: Store partial_data as JSON in Resume model (work history array, education array); full only after unlock
   - Unlock model: employer_id, resume_id, paid_amount, stripe_charge_id
   - Gate/Policy: employer can view full if unlocked or owns subscription that allows unlimited views
   - Custom job apply modal (Livewire):
     - Pop-up on "Apply" button
     - Dropdown: select existing resume OR file upload (new resume PDF/doc)
     - Textarea: cover letter (paste/type, 2000 char max)
     - Submit → create Application with cover_letter + resume_path/reference
   - Dynamic locations (admin setting: 1=City only, 2=Province > City, 3=Country > Province > City)
     - Use cascading selects (Livewire or Alpine) in search forms, job post, profiles
     - Models: Country, Province (State), City with relations
   - Homepage search: change to "Job title/keywords" + "Location" (multi-level aware)
   - Secure admin: obfuscated URL prefix from .env (e.g. /admin-xyz123)

Tech stack assumptions: Use existing auth (probably Laravel Breeze/Jetstream), Blade + Livewire components, Tailwind, Stripe Cashier (recurring + one-time).

Start by:
- Suggesting new models/migrations (Package, Subscription, Unlock, CreditTransaction, Location models)
- Then provide code for resume list partial view + unlock payment flow (Livewire component)
- Then Packages/Subscriptions page (Blade + Livewire for tabs/selection/checkout)
- Include Tailwind classes to match screenshots (e.g. row/grid layout, blue buttons, rebate green badges, radio with circle, etc.)
- Use SVG icons where possible (check, arrow-right, lock, flag, payment logos)

Output in markdown: file paths, code blocks, step-by-step instructions, and any new routes/controllers/views.
If something needs clarification (e.g. current DB schema), note it and proceed with best assumptions for a typical job board.