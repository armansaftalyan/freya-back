<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Users\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_register_login_and_logout(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $register = $this->postJson('/api/auth/register', [
            'name' => 'Test Client',
            'email' => 'new-client@example.com',
            'phone' => '+12025550111',
            'password' => 'password123',
        ]);

        $register->assertCreated()
            ->assertJsonPath('user.email', 'new-client@example.com')
            ->assertJsonStructure(['token', 'user' => ['id', 'email']]);

        $token = (string) $register->json('token');

        $this->getJson('/api/auth/me', ['Authorization' => 'Bearer '.$token])
            ->assertOk()
            ->assertJsonPath('data.email', 'new-client@example.com');

        $this->postJson('/api/auth/logout', [], ['Authorization' => 'Bearer '.$token])
            ->assertOk();

        $this->getJson('/api/auth/me', ['Authorization' => 'Bearer '.$token])
            ->assertUnauthorized();
    }

    public function test_existing_user_can_login(): void
    {
        $this->seed(RolePermissionSeeder::class);

        /** @var User $user */
        $user = User::query()->create([
            'name' => 'Client',
            'email' => 'client-login@example.com',
            'password' => 'password',
        ]);
        $user->assignRole('client');

        $this->postJson('/api/auth/login', [
            'email' => 'client-login@example.com',
            'password' => 'password',
        ])->assertOk()->assertJsonStructure(['token', 'user']);
    }
}
