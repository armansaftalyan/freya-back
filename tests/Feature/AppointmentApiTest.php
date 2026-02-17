<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Branch;
use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Domain\Users\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_and_cancel_appointment(): void
    {
        [$client, $master, $service, $branch] = $this->createCatalogContext();

        Sanctum::actingAs($client);

        $create = $this->postJson('/api/appointments', [
            'master_id' => $master->id,
            'branch_id' => $branch->id,
            'service_id' => $service->id,
            'start_at' => now()->addDay()->setTime(10, 0)->toDateTimeString(),
            'source' => 'site',
        ]);

        $create->assertCreated();

        $appointmentId = (int) $create->json('data.id');

        $cancel = $this->patchJson('/api/appointments/'.$appointmentId.'/cancel');
        $cancel->assertOk()->assertJsonPath('data.status', AppointmentStatus::Cancelled->value);
    }

    public function test_creation_fails_when_master_has_time_overlap(): void
    {
        [$client, $master, $service, $branch] = $this->createCatalogContext();

        Appointment::query()->create([
            'client_id' => $client->id,
            'master_id' => $master->id,
            'branch_id' => $branch->id,
            'service_id' => $service->id,
            'start_at' => now()->addDays(2)->setTime(11, 0),
            'end_at' => now()->addDays(2)->setTime(12, 0),
            'status' => AppointmentStatus::Pending,
            'source' => 'site',
        ]);

        Sanctum::actingAs($client);

        $this->postJson('/api/appointments', [
            'master_id' => $master->id,
            'branch_id' => $branch->id,
            'service_id' => $service->id,
            'start_at' => now()->addDays(2)->setTime(11, 30)->toDateTimeString(),
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['start_at']);
    }

    /** @return array{User, Master, Service, Branch} */
    private function createCatalogContext(): array
    {
        $this->seed(RolePermissionSeeder::class);

        /** @var User $client */
        $client = User::query()->create([
            'name' => 'Client',
            'email' => 'client@example.test',
            'password' => 'password',
        ]);
        $client->assignRole('client');

        /** @var User $masterUser */
        $masterUser = User::query()->create([
            'name' => 'Master User',
            'email' => 'master@example.test',
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
            'name' => 'Haircut',
            'slug' => 'haircut',
            'duration_minutes' => 60,
            'price_from' => 50,
            'price_to' => 70,
            'sort' => 10,
            'is_active' => true,
        ]);

        $branch = Branch::query()->create([
            'name' => 'Branch 1',
            'working_hours' => [
                'monday' => [['start' => '09:00', 'end' => '19:00']],
                'tuesday' => [['start' => '09:00', 'end' => '19:00']],
                'wednesday' => [['start' => '09:00', 'end' => '19:00']],
                'thursday' => [['start' => '09:00', 'end' => '19:00']],
                'friday' => [['start' => '09:00', 'end' => '19:00']],
                'saturday' => [['start' => '10:00', 'end' => '17:00']],
                'sunday' => [],
            ],
            'is_active' => true,
        ]);

        $master = Master::query()->create([
            'user_id' => $masterUser->id,
            'name' => 'Master A',
            'is_active' => true,
            'schedule_rules' => [
                'monday' => [['start' => '09:00', 'end' => '19:00']],
                'tuesday' => [['start' => '09:00', 'end' => '19:00']],
                'wednesday' => [['start' => '09:00', 'end' => '19:00']],
                'thursday' => [['start' => '09:00', 'end' => '19:00']],
                'friday' => [['start' => '09:00', 'end' => '19:00']],
                'saturday' => [['start' => '10:00', 'end' => '17:00']],
                'sunday' => [],
            ],
        ]);

        $master->services()->attach($service->id);

        return [$client, $master, $service, $branch];
    }
}
