<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_cannot_access_cv_management()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)
                         ->withSession(['user_role' => 'super_admin'])
                         ->get('/backoffice/cv');

        $this->assertTrue($response->isRedirect() || $response->status() === 403);
    }

    public function test_hr_manager_can_access_cv_management()
    {
        $hrManager = User::factory()->create(['role' => 'hr_manager']);

        $response = $this->actingAs($hrManager)
                         ->withSession(['user_role' => 'hr_manager'])
                         ->get('/backoffice/cv');

        $response->assertStatus(200);
    }
}
