<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Domain\Users\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_master_profile_with_services_and_certificates(): void
    {
        $this->seed(RolePermissionSeeder::class);

        /** @var User $masterUser */
        $masterUser = User::query()->create([
            'name' => 'Master User',
            'email' => 'master.profile@example.test',
            'password' => 'password',
        ]);
        $masterUser->assignRole('master');

        $category = Category::query()->create([
            'name' => 'Hair',
            'slug' => 'hair',
            'sort' => 10,
            'is_active' => true,
        ]);

        $service = Service::query()->create([
            'category_id' => $category->id,
            'name' => 'Premium Haircut',
            'slug' => 'premium-haircut',
            'duration_minutes' => 70,
            'price_from' => 70,
            'price_to' => 100,
            'sort' => 10,
            'is_active' => true,
        ]);

        $master = Master::query()->create([
            'user_id' => $masterUser->id,
            'name' => 'Master Profile',
            'bio' => 'Detailed profile',
            'experience_years' => 8,
            'specialties' => ['Haircut', 'Coloring'],
            'languages' => ['Armenian', 'Russian'],
            'certificates' => [
                ['title' => 'Advanced Haircut', 'issuer' => 'Academy', 'year' => 2023, 'image' => 'https://example.com/cert-1.jpg'],
            ],
            'instagram' => '@master.profile',
            'is_active' => true,
            'schedule_rules' => [
                'monday' => [['start' => '09:00', 'end' => '19:00']],
            ],
        ]);

        $master->services()->attach($service->id, ['duration_minutes' => 75, 'price' => 95]);

        $response = $this->getJson('/api/masters/'.$master->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $master->id)
            ->assertJsonPath('data.experience_years', 8)
            ->assertJsonPath('data.specialties.0', 'Haircut')
            ->assertJsonPath('data.certificates.0.title', 'Advanced Haircut')
            ->assertJsonPath('data.services.0.name', 'Premium Haircut')
            ->assertJsonPath('data.services.0.duration_minutes', 75)
            ->assertJsonPath('data.services.0.price', 95);
    }
}
