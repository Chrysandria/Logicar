<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RouteAccessTest extends TestCase
{
    public function test_the_homepage_displays_the_landing_page_and_login_remains_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Le transport du personnel,')
            ->assertSee('organisé simplement.')
            ->assertSee('href="' . route('login') . '"', false);

        $this->get('/login')
            ->assertOk();
    }

    public function test_application_workspaces_and_data_routes_require_authentication(): void
    {
        foreach (['/snr', '/other_service', '/movements', '/session'] as $path) {
            $this->get($path)->assertRedirect('/login');
        }
    }

    public function test_users_cannot_access_the_other_service_workspace_or_snr_data(): void
    {
        $user = new User;
        $user->forceFill(['id' => 2, 'name' => 'operator', 'service_sigle' => 'OPS']);

        $this->actingAs($user)->getJson('/snr')->assertForbidden();
        $this->getJson('/cars')->assertForbidden();
    }

    public function test_snr_users_cannot_access_another_service_workspace(): void
    {
        $user = new User;
        $user->forceFill(['id' => 3, 'name' => 'manager', 'service_sigle' => 'SNR']);

        $this->actingAs($user)->getJson('/other_service')->assertForbidden();
    }
}
