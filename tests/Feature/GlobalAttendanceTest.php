<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GlobalAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_global_attendance()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)
                         ->withSession(['user_role' => 'super_admin'])
                         ->get('/backoffice/super-admin/absensi');

        $response->assertStatus(200);
        $response->assertSee('Monitoring Absensi Global');
    }
}
