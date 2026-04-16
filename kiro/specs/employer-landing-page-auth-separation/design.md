# Design Document: Employer Landing Page Auth Separation

## Overview

This feature creates a dedicated employer landing page with dynamic content management and separates employer authentication from job seeker authentication flows. The system will provide a marketing-focused landing page for employers before authentication, while job seekers continue to access authentication directly from the homepage.

The design follows Laravel's MVC architecture and leverages the existing CMS infrastructure for content management. The solution ensures backward compatibility with existing employer accounts while providing a clear separation of concerns between user types.

### Key Components

- Employer landing page with dynamic HTML content
- Separate authentication routes and controllers for employers and job seekers
- Admin panel HTML editor for landing page content management
- Route redirection logic for "Employers/Post Job" link
- Database storage for landing page content using existing CMS tables

### Design Goals

1. Clear separation between employer and job seeker authentication flows
2. Marketing-focused employer landing page with customizable content
3. Admin-friendly content management without requiring code deployment
4. Backward compatibility with existing employer accounts
5. Security through HTML sanitization and XSS prevention
6. Maintainable codebase with proper separation of concerns

## Architecture

### System Architecture

The feature follows Laravel's standard MVC pattern with these architectural layers:

```
┌─────────────────────────────────────────────────────────────┐
│                        Presentation Layer                    │
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │  Homepage        │  │  Employer        │                 │
│  │  (Job Seekers)   │  │  Landing Page    │                 │
│  └──────────────────┘  └──────────────────┘                 │
│           │                      │                           │
└───────────┼──────────────────────┼───────────────────────────┘
            │                      │
┌───────────┼──────────────────────┼───────────────────────────┐
│           │    Application Layer │                           │
│  ┌────────▼────────┐    ┌───────▼────────┐                 │
│  │  Job Seeker     │    │  Employer      │                  │
│  │  Auth           │    │  Auth          │                  │
│  │  Controllers    │    │  Controllers   │                  │
│  └─────────────────┘    └────────────────┘                  │
│           │                      │                           │
│  ┌────────▼──────────────────────▼────────┐                │
│  │     Admin CMS Controller               │                 │
│  │  (Landing Page Content Management)     │                 │
│  └────────────────────────────────────────┘                 │
└───────────┼──────────────────────┼───────────────────────────┘
            │                      │
┌───────────┼──────────────────────┼───────────────────────────┐
│           │      Data Layer      │                           │
│  ┌────────▼────────┐    ┌───────▼────────┐                 │
│  │  users table    │    │  companies     │                  │
│  │  (Job Seekers)  │    │  table         │                  │
│  └─────────────────┘    │  (Employers)   │                  │
│                          └────────────────┘                  │
│  ┌──────────────────────────────────────┐                   │
│  │  cms & cms_content tables            │                   │
│  │  (Landing Page Content)              │                   │
│  └──────────────────────────────────────┘                   │
└─────────────────────────────────────────────────────────────┘
```

### Authentication Flow Separation

The system maintains two distinct authentication flows:

**Job Seeker Flow:**
```
Homepage → Login/Register Modal → Auth Routes → Job Seeker Dashboard
```

**Employer Flow:**
```
Homepage → "Employers/Post Job" Link → Landing Page → Login/Register → Employer Dashboard
```

### Route Structure

```
/                           → Homepage (Job Seeker focused)
/for-employers              → Employer Landing Page (new)
/company/login              → Employer Login (existing, accessed via landing page)
/company/register           → Employer Registration (existing, accessed via landing page)
/login                      → Job Seeker Login (existing)
/register                   → Job Seeker Registration (existing)
/admin/employer-landing     → Admin CMS Editor (new)
```

## Components and Interfaces

### 1. Employer Landing Page Controller

**Purpose:** Handles requests to the employer landing page and retrieves dynamic content from the database.

**Location:** `app/Http/Controllers/EmployerLandingController.php`

**Methods:**
- `index()`: Displays the employer landing page with content from database
  - Retrieves landing page content from CMS
  - Falls back to default content if none exists
  - Returns view with sanitized HTML content

**Dependencies:**
- `App\Cms` model
- `App\CmsContent` model
- HTML Purifier for sanitization

### 2. Admin Landing Page CMS Controller

**Purpose:** Manages the employer landing page content through the admin panel.

**Location:** `app/Http/Controllers/Admin/EmployerLandingController.php`

**Methods:**
- `edit()`: Displays the HTML editor form
  - Loads current landing page content
  - Provides TinyMCE WYSIWYG editor
- `update(Request $request)`: Saves landing page content
  - Validates HTML input
  - Sanitizes content to prevent XSS
  - Stores in cms_content table
  - Returns success/error message

**Dependencies:**
- `App\Cms` model
- `App\CmsContent` model
- HTML Purifier library
- TinyMCE editor (already in project)

### 3. Route Middleware

**Purpose:** Ensures proper route protection and user type separation.

**Existing Middleware to Use:**
- `auth:web` - For job seeker routes
- `auth:company` - For employer routes
- `guest` - For public pages

**No new middleware required** - the existing authentication guards already provide the necessary separation.

### 4. Database Models

**Cms Model** (existing)
- Location: `app/Cms.php`
- Purpose: Manages CMS pages
- Fields: id, page_slug, show_in_top_menu, show_in_footer_menu, seo_title, seo_description, seo_keywords, seo_other

**CmsContent Model** (existing)
- Location: `app/CmsContent.php`
- Purpose: Stores page content with multi-language support
- Fields: id, page_id, page_title, page_content, lang, created_at, updated_at

**Usage for Landing Page:**
- Create a CMS entry with slug: `employer-landing-page`
- Store HTML content in cms_content.page_content
- Support multi-language content through lang field

### 5. Views

**Employer Landing Page View**
- Location: `resources/views/employer/landing.blade.php`
- Purpose: Displays the employer landing page
- Features:
  - Hero section with value proposition
  - Benefits showcase sections
  - Verified employer badge information
  - Clear CTAs for login/registration
  - Dynamic content from database
  - Responsive design matching site theme

**Admin CMS Editor View**
- Location: `resources/views/admin/employer_landing/edit.blade.php`
- Purpose: Provides HTML editor for admin
- Features:
  - TinyMCE WYSIWYG editor
  - Preview functionality
  - Save/Cancel buttons
  - Validation error display

**Homepage Updates**
- Location: `resources/views/includes/header.blade.php`
- Changes: Update "Employers/Post Job" link to point to landing page

## Data Models

### CMS Page Entry

```php
// New CMS entry for employer landing page
[
    'page_slug' => 'employer-landing-page',
    'show_in_top_menu' => false,
    'show_in_footer_menu' => false,
    'seo_title' => 'Employer Zone - Post Jobs & Hire Talent',
    'seo_description' => 'Join our employer platform to post jobs, access qualified candidates, and grow your team.',
    'seo_keywords' => 'employer, post job, hire, recruitment',
]
```

### CMS Content Entry

```php
// Content for employer landing page
[
    'page_id' => <cms_id>,
    'page_title' => 'Employer Zone',
    'page_content' => '<html content>', // Sanitized HTML
    'lang' => 'en',
]
```

### Default Landing Page Content Structure

The default content will include:

1. **Hero Section**
   - Headline: "Hire globally, faster and smarter"
   - Subheadline: Value proposition
   - CTA buttons: Login / Register

2. **Benefits Section**
   - Faster hiring process
   - Access to more applicants
   - AI-powered matching features
   - Verified employer badge benefits

3. **How It Works Section**
   - Step 1: Create company profile
   - Step 2: Post a job
   - Step 3: Hire job seekers

4. **Features Section**
   - Streamlined sourcing
   - Instant onboarding
   - Seamless management

5. **Call to Action Section**
   - Final CTA to register/login

### HTML Sanitization Rules

To prevent XSS attacks while allowing rich content:

**Allowed HTML Tags:**
- Structure: `div`, `section`, `article`, `header`, `footer`, `main`
- Text: `h1-h6`, `p`, `span`, `strong`, `em`, `ul`, `ol`, `li`, `a`
- Media: `img`, `svg`, `video` (with attribute restrictions)
- Layout: `table`, `tr`, `td`, `th`

**Allowed Attributes:**
- `class`, `id` (for styling)
- `href` (for links, validated for safe protocols)
- `src`, `alt` (for images, validated for safe sources)
- `style` (limited to safe CSS properties)

**Forbidden:**
- `<script>` tags
- `onclick`, `onerror`, and other event handlers
- `javascript:` protocol in URLs
- `<iframe>` tags (unless explicitly needed and sandboxed)

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*


### Property Reflection

After analyzing all acceptance criteria, I identified the following properties that need testing. Many criteria are specific examples or edge cases rather than universal properties. The properties below represent the universal rules that should hold across all valid inputs:

**Properties identified:**
- Content persistence round-trip (6.3, 6.4, 6.6, 7.2)
- HTML sanitization for security (6.5, 7.4)
- Authentication routing based on user type (8.4, 8.5)
- Backward compatibility for existing users (10.2, 10.3, 10.4)

**Redundancy elimination:**
- Properties 6.3, 6.4, 6.6, and 7.2 all relate to content persistence and can be combined into a single comprehensive round-trip property
- Properties 6.5 and 7.4 both relate to HTML sanitization and can be combined
- Properties 8.4 and 8.5 are similar patterns for different user types and can be combined into one property
- Properties 10.2, 10.3, and 10.4 all relate to backward compatibility and can be combined

### Property 1: Content Persistence Round-Trip

*For any* valid HTML content saved through the admin editor, retrieving and rendering that content on the landing page should produce the same HTML structure and formatting.

**Validates: Requirements 6.3, 6.4, 6.6, 7.2**

### Property 2: HTML Sanitization for XSS Prevention

*For any* HTML content containing potentially dangerous elements (script tags, event handlers, javascript: protocols), the system should sanitize or reject the content before storage and rendering, preventing XSS attacks.

**Validates: Requirements 6.5, 7.4**

### Property 3: User Type Based Authentication Routing

*For any* authenticated user (employer or job seeker), the system should redirect them to their respective dashboard based on their user type after successful authentication.

**Validates: Requirements 8.4, 8.5**

### Property 4: Backward Compatibility for Existing Employers

*For any* existing employer account with valid credentials, the authentication, session management, and authorization should continue to function identically to the pre-feature behavior.

**Validates: Requirements 10.2, 10.3, 10.4**

## Error Handling

### Input Validation Errors

**HTML Content Validation:**
- **Error:** Invalid HTML structure
- **Handling:** Display validation error message to admin, do not save
- **User Feedback:** "Invalid HTML structure. Please check your markup."

- **Error:** Content exceeds maximum size (e.g., 1MB)
- **Handling:** Reject submission, display error
- **User Feedback:** "Content is too large. Maximum size is 1MB."

- **Error:** Dangerous HTML detected (XSS attempt)
- **Handling:** Sanitize content automatically, log security event
- **User Feedback:** "Some potentially unsafe content was removed for security."

### Database Errors

**CMS Content Save Failure:**
- **Error:** Database connection failure
- **Handling:** Catch exception, rollback transaction, display error
- **User Feedback:** "Unable to save content. Please try again."
- **Logging:** Log full error details for debugging

**CMS Content Retrieval Failure:**
- **Error:** Database query failure
- **Handling:** Fall back to default content, log error
- **User Feedback:** Display default landing page content
- **Logging:** Log error for investigation

### Authentication Errors

**Route Access Errors:**
- **Error:** Unauthenticated user accessing protected route
- **Handling:** Redirect to appropriate login page based on route type
- **User Feedback:** "Please log in to continue."

- **Error:** Wrong user type accessing route (e.g., job seeker accessing employer route)
- **Handling:** Redirect to appropriate dashboard or show 403 error
- **User Feedback:** "You don't have permission to access this page."

### Content Rendering Errors

**Missing Content:**
- **Error:** No landing page content in database
- **Handling:** Display default hardcoded content
- **User Feedback:** None (seamless fallback)
- **Logging:** Log warning for admin awareness

**Malformed HTML:**
- **Error:** Stored HTML cannot be rendered properly
- **Handling:** Attempt to render with error suppression, fall back to default if critical
- **User Feedback:** Display best-effort rendering or default content
- **Logging:** Log error with content details

### Admin Panel Errors

**Permission Errors:**
- **Error:** Non-admin user attempting to access CMS editor
- **Handling:** Return 403 Forbidden
- **User Feedback:** "Access denied. Admin privileges required."

**Concurrent Edit Conflicts:**
- **Error:** Multiple admins editing simultaneously
- **Handling:** Last write wins (acceptable for this use case)
- **User Feedback:** None (or optional: "Content was updated by another admin")

## Testing Strategy

### Dual Testing Approach

This feature will use both unit tests and property-based tests to ensure comprehensive coverage:

**Unit Tests** will focus on:
- Specific examples of route behavior (e.g., /for-employers returns 200)
- Edge cases like missing content, empty database
- Integration points between controllers and models
- Admin panel form submission and validation
- Specific HTML sanitization examples

**Property-Based Tests** will focus on:
- Universal properties that hold for all inputs
- Content round-trip integrity across random HTML inputs
- XSS prevention across various attack vectors
- Authentication routing for all user types
- Backward compatibility across existing user data

### Property-Based Testing Configuration

**Framework:** Use Laravel's built-in testing with a PHP property-based testing library such as `eris/eris` or implement custom generators.

**Configuration:**
- Minimum 100 iterations per property test
- Each test tagged with feature name and property reference
- Tag format: `@test Feature: employer-landing-page-auth-separation, Property {number}: {property_text}`

### Unit Test Coverage

**Route Tests:**
```php
// Test employer landing page route exists
test_employer_landing_page_route_accessible()
test_employer_landing_page_returns_200()
test_post_job_link_redirects_to_landing_page()
```

**Authentication Tests:**
```php
// Test separate auth routes
test_employer_login_route_exists()
test_employer_register_route_exists()
test_job_seeker_login_route_exists()
test_job_seeker_register_route_exists()
test_homepage_shows_job_seeker_auth_only()
```

**Admin CMS Tests:**
```php
// Test admin editor functionality
test_admin_can_access_landing_page_editor()
test_non_admin_cannot_access_editor()
test_editor_displays_current_content()
test_saving_content_updates_database()
```

**Content Rendering Tests:**
```php
// Test content display
test_landing_page_displays_database_content()
test_landing_page_falls_back_to_default_when_no_content()
test_html_is_sanitized_before_rendering()
```

**Backward Compatibility Tests:**
```php
// Test existing functionality preserved
test_existing_employer_can_login()
test_existing_employer_redirected_to_dashboard()
test_existing_employer_permissions_unchanged()
```

### Property-Based Test Specifications

**Property 1: Content Persistence Round-Trip**
```php
/**
 * @test
 * Feature: employer-landing-page-auth-separation
 * Property 1: For any valid HTML content saved through the admin editor,
 * retrieving and rendering that content should produce the same structure
 */
property_content_round_trip_preserves_structure()
{
    // Generate random valid HTML content
    // Save through admin controller
    // Retrieve from database
    // Assert structure is preserved
}
```

**Property 2: HTML Sanitization**
```php
/**
 * @test
 * Feature: employer-landing-page-auth-separation
 * Property 2: For any HTML containing dangerous elements,
 * the system should sanitize before storage and rendering
 */
property_dangerous_html_is_sanitized()
{
    // Generate HTML with various XSS vectors
    // Save through admin controller
    // Retrieve and render
    // Assert dangerous elements are removed
}
```

**Property 3: User Type Based Routing**
```php
/**
 * @test
 * Feature: employer-landing-page-auth-separation
 * Property 3: For any authenticated user, redirect to correct dashboard
 * based on user type
 */
property_authentication_routes_by_user_type()
{
    // Generate random user of type employer or job seeker
    // Authenticate user
    // Assert redirected to correct dashboard
}
```

**Property 4: Backward Compatibility**
```php
/**
 * @test
 * Feature: employer-landing-page-auth-separation
 * Property 4: For any existing employer account, authentication and
 * authorization should work identically to pre-feature behavior
 */
property_existing_employers_maintain_functionality()
{
    // Use existing employer accounts from database
    // Test authentication, session, and permissions
    // Assert all functionality works as before
}
```

### Integration Testing

**End-to-End User Flows:**
1. Job seeker visits homepage → clicks login → authenticates → reaches job seeker dashboard
2. Employer visits homepage → clicks "Employers/Post Job" → views landing page → clicks login → authenticates → reaches employer dashboard
3. Admin logs in → navigates to landing page editor → edits content → saves → views landing page → sees updated content

**Browser Testing:**
- Test on Chrome, Firefox, Safari
- Test responsive design on mobile devices
- Test TinyMCE editor functionality across browsers

### Security Testing

**XSS Prevention:**
- Test various XSS attack vectors in HTML editor
- Verify script tags are stripped
- Verify event handlers are removed
- Verify javascript: protocols are blocked

**Authentication Security:**
- Test that employer routes require employer authentication
- Test that job seeker routes require job seeker authentication
- Test that admin routes require admin authentication
- Test CSRF protection on all forms

### Performance Testing

**Page Load Times:**
- Landing page should load in < 2 seconds
- Admin editor should load in < 3 seconds
- Content save should complete in < 1 second

**Database Queries:**
- Landing page should require ≤ 3 database queries
- Admin editor should require ≤ 5 database queries

### Manual Testing Checklist

- [ ] Employer landing page displays correctly with default content
- [ ] Admin can edit landing page content through editor
- [ ] Saved content appears on landing page immediately
- [ ] "Employers/Post Job" link redirects to landing page
- [ ] Employer login/register links work from landing page
- [ ] Job seeker auth works from homepage
- [ ] Existing employer accounts can still log in
- [ ] HTML sanitization prevents XSS attacks
- [ ] Responsive design works on mobile
- [ ] Multi-language support works (if applicable)

