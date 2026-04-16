# Implementation Plan: Employer Landing Page Auth Separation

## Overview

This implementation creates a dedicated employer landing page with dynamic CMS content management and separates employer authentication from job seeker authentication flows. The feature leverages Laravel's existing CMS infrastructure and authentication guards to provide a marketing-focused landing page for employers while maintaining backward compatibility with existing accounts.

## Tasks

- [x] 1. Set up database structure and CMS entry
  - Create migration for CMS page entry with slug 'employer-landing-page'
  - Seed default CMS content for the landing page
  - Ensure cms and cms_content tables are properly configured
  - _Requirements: 1.1, 6.6, 7.1, 7.3_

- [x] 2. Create employer landing page controller and route
  - [x] 2.1 Implement EmployerLandingController with index method
    - Create controller at app/Http/Controllers/EmployerLandingController.php
    - Implement content retrieval from CMS tables
    - Add fallback to default content when database content is empty
    - Implement HTML sanitization using HTML Purifier
    - _Requirements: 1.1, 1.2, 7.1, 7.2, 7.3, 7.4_
  
  - [ ]* 2.2 Write property test for content round-trip preservation
    - **Property 1: Content Persistence Round-Trip**
    - **Validates: Requirements 6.3, 6.4, 6.6, 7.2**
  
  - [x] 2.3 Add route for employer landing page
    - Register /for-employers route in web.php
    - Point route to EmployerLandingController@index
    - _Requirements: 1.1, 5.2_

- [x] 3. Create employer landing page view
  - [x] 3.1 Design and implement landing page Blade template
    - Create resources/views/employer/landing.blade.php
    - Implement hero section with value proposition
    - Add benefits showcase sections
    - Include verified employer badge information
    - Add clear CTAs for login and registration
    - Ensure responsive design matching site theme
    - _Requirements: 1.2, 1.3, 1.4, 9.1, 9.2, 9.5_
  
  - [ ]* 3.2 Write unit tests for landing page view rendering
    - Test that landing page displays database content
    - Test fallback to default content when database is empty
    - Test that CTAs link to correct authentication routes
    - _Requirements: 1.2, 7.3_

- [x] 4. Update homepage navigation
  - [x] 4.1 Modify "Employers/Post Job" link to redirect to landing page
    - Update resources/views/includes/header.blade.php
    - Change link target from authentication popup to /for-employers route
    - Ensure link remains clearly visible
    - _Requirements: 2.1, 2.2, 2.3_
  
  - [x] 4.2 Add "For Employers" link if not present
    - Add employer access link to homepage navigation
    - Ensure visual distinction from job seeker elements
    - _Requirements: 5.1, 5.2, 5.3_
  
  - [ ]* 4.3 Write unit tests for homepage navigation
    - Test that "Employers/Post Job" link points to /for-employers
    - Test that "For Employers" link is present and functional
    - _Requirements: 2.1, 5.1_

- [x] 5. Checkpoint - Verify landing page and navigation
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Implement admin CMS editor for landing page
  - [x] 6.1 Create admin controller for landing page management
    - Create app/Http/Controllers/Admin/EmployerLandingController.php
    - Implement edit() method to display editor form
    - Implement update() method to save content
    - Add HTML validation and sanitization
    - Add success/error flash messages
    - _Requirements: 6.1, 6.3, 6.5, 6.6_
  
  - [ ]* 6.2 Write property test for HTML sanitization
    - **Property 2: HTML Sanitization for XSS Prevention**
    - **Validates: Requirements 6.5, 7.4**
  
  - [x] 6.3 Create admin editor view
    - Create resources/views/admin/employer_landing/edit.blade.php
    - Integrate TinyMCE WYSIWYG editor
    - Add preview functionality
    - Add save and cancel buttons
    - Display validation errors
    - _Requirements: 6.1, 6.2, 6.4_
  
  - [x] 6.4 Add admin routes for landing page editor
    - Register GET /admin/employer-landing route for edit form
    - Register POST /admin/employer-landing route for update
    - Protect routes with admin middleware
    - _Requirements: 6.1_
  
  - [ ]* 6.5 Write unit tests for admin CMS functionality
    - Test that admin can access editor
    - Test that non-admin cannot access editor
    - Test that saving content updates database
    - Test validation error handling
    - _Requirements: 6.1, 6.3, 6.5_

- [x] 7. Separate authentication flows
  - [x] 7.1 Update employer authentication routes
    - Ensure /company/login and /company/register routes exist
    - Remove any employer authentication popups from homepage
    - Ensure employer routes use auth:company middleware
    - _Requirements: 3.1, 3.2, 3.3, 3.5, 8.1, 8.2, 8.3_
  
  - [x] 7.2 Update job seeker authentication on homepage
    - Ensure homepage displays login button for job seekers
    - Ensure homepage displays registration button for job seekers
    - Remove employer authentication options from homepage
    - Ensure job seeker routes use auth:web middleware
    - _Requirements: 3.4, 4.1, 4.2, 4.3, 4.4, 4.5, 8.1, 8.2, 8.3_
  
  - [ ]* 7.3 Write property test for user type based routing
    - **Property 3: User Type Based Authentication Routing**
    - **Validates: Requirements 8.4, 8.5**
  
  - [ ]* 7.4 Write unit tests for authentication flow separation
    - Test employer login route exists and uses correct middleware
    - Test employer register route exists and uses correct middleware
    - Test job seeker login route exists and uses correct middleware
    - Test job seeker register route exists and uses correct middleware
    - Test homepage shows only job seeker authentication
    - _Requirements: 3.1, 3.2, 3.4, 4.1, 4.2_

- [x] 8. Implement post-authentication redirects
  - [x] 8.1 Configure employer dashboard redirect
    - Update employer authentication controller to redirect to employer dashboard
    - Ensure redirect happens after successful login
    - _Requirements: 8.4_
  
  - [x] 8.2 Configure job seeker dashboard redirect
    - Update job seeker authentication controller to redirect to job seeker dashboard
    - Ensure redirect happens after successful login
    - _Requirements: 8.5_
  
  - [ ]* 8.3 Write unit tests for post-authentication redirects
    - Test employer redirected to employer dashboard after login
    - Test job seeker redirected to job seeker dashboard after login
    - _Requirements: 8.4, 8.5_

- [x] 9. Checkpoint - Verify authentication separation
  - Ensure all tests pass, ask the user if questions arise.

- [x] 10. Ensure backward compatibility
  - [x] 10.1 Verify existing employer accounts work
    - Test that existing employer users can authenticate with current credentials
    - Test that existing employer sessions continue to work
    - Test that existing employer authorization rules are preserved
    - _Requirements: 10.1, 10.2, 10.3, 10.4_
  
  - [ ]* 10.2 Write property test for backward compatibility
    - **Property 4: Backward Compatibility for Existing Employers**
    - **Validates: Requirements 10.2, 10.3, 10.4**
  
  - [ ]* 10.3 Write unit tests for backward compatibility
    - Test existing employer can login with credentials
    - Test existing employer session management works
    - Test existing employer permissions unchanged
    - _Requirements: 10.2, 10.3, 10.4_

- [x] 11. Add SEO and meta tags
  - [x] 11.1 Configure SEO metadata for landing page
    - Set seo_title, seo_description, seo_keywords in CMS entry
    - Add Open Graph tags to landing page view
    - Add Twitter Card tags to landing page view
    - _Requirements: 1.1_
  
  - [ ]* 11.2 Write unit tests for SEO metadata
    - Test that landing page includes correct meta tags
    - Test that SEO data is retrieved from CMS
    - _Requirements: 1.1_

- [x] 12. Final integration and testing
  - [x] 12.1 Test complete employer user journey
    - Test: Homepage → "Employers/Post Job" → Landing Page → Login → Dashboard
    - Test: Homepage → "For Employers" → Landing Page → Register → Dashboard
    - _Requirements: 2.1, 5.2, 9.3, 9.4_
  
  - [x] 12.2 Test complete job seeker user journey
    - Test: Homepage → Login → Dashboard
    - Test: Homepage → Register → Dashboard
    - _Requirements: 4.4, 4.5_
  
  - [x] 12.3 Test admin content management workflow
    - Test: Admin Panel → Landing Page Editor → Edit Content → Save → View Landing Page
    - _Requirements: 6.1, 6.3, 7.1, 7.2_
  
  - [ ]* 12.4 Write integration tests for complete user journeys
    - Test employer journey from homepage to dashboard
    - Test job seeker journey from homepage to dashboard
    - Test admin content update workflow
    - _Requirements: 2.1, 4.4, 6.3_

- [x] 13. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- Property tests validate universal correctness properties across all inputs
- Unit tests validate specific examples, edge cases, and integration points
- The feature leverages existing Laravel authentication guards (auth:web, auth:company)
- HTML Purifier library should be used for content sanitization
- TinyMCE editor is already available in the project for WYSIWYG editing
- Backward compatibility is critical - existing employer accounts must continue working
