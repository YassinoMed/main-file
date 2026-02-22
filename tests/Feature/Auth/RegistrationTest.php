<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $this->assertSame('web', config('auth.defaults.guard'));

        Role::create([
            'name' => 'company',
            'guard_name' => 'web',
            'created_by' => 0,
        ]);

        $response = $this->post('/register/store', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => 'on',
            'ref_code' => 0,
        ]);

        $response->assertStatus(302);
        $response->assertSessionMissing('status');
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
