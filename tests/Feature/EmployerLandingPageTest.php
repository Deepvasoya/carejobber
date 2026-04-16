<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployerLandingPageTest extends TestCase
{
    /**
     * Test that the employer landing page route is accessible.
     *
     * @return void
     */
    public function test_employer_landing_page_route_accessible()
    {
        $response = $this->get('/for-employers');

        $response->assertStatus(200);
    }

    /**
     * Test that the employer landing page displays content.
     *
     * @return void
     */
    public function test_employer_landing_page_displays_content()
    {
        $response = $this->get('/for-employers');

        $response->assertStatus(200);
        $response->assertSee('Employer Zone');
    }
}
