<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Salon\Models\Appointment;
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
        [$client, $master, $service] = $this->createCatalogContext();

        Sanctum::actingAs($client);

        $create = $this->postJson('/api/appointments', [
            'master_id' => $master->id,
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
        [$client, $master, $service] = $this->createCatalogContext();

        Appointment::query()->create([
            'client_id' => $client->id,
            'master_id' => $master->id,
            'service_id' => $service->id,
            'start_at' => now()->addDays(2)->setTime(11, 0),
            'end_at' => now()->addDays(2)->setTime(12, 0),
            'status' => AppointmentStatus::Pending,
            'source' => 'site',
        ]);

        Sanctum::actingAs($client);

        $this->postJson('/api/appointments', [
            'master_id' => $master->id,
            'service_id' => $service->id,
            'start_at' => now()->addDays(2)->setTime(11, 30)->toDateTimeString(),
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['start_at']);
    }

    public function test_guest_can_create_appointment_without_authorization(): void
    {
        [, $master, $service] = $this->createCatalogContext();

        $create = $this->postJson('/api/appointments', [
            'master_id' => $master->id,
            'service_id' => $service->id,
            'start_at' => now()->addDay()->setTime(13, 0)->toDateTimeString(),
            'source' => 'yandex_maps',
            'guest_name' => 'Guest Client',
            'guest_phone' => '+15551230001',
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.source', 'yandex_maps');

        $this->assertDatabaseHas('users', [
            'name' => 'Guest Client',
            'phone' => '+15551230001',
        ]);
    }

    public function test_client_can_create_appointment_with_multiple_services(): void
    {
        [$client, $master, $service] = $this->createCatalogContext();

        $secondService = Service::query()->create([
            'category_id' => $service->category_id,
            'name' => 'Styling',
            'slug' => 'styling',
            'duration_minutes' => 30,
            'price_from' => 25,
            'price_to' => 40,
            'sort' => 20,
            'is_active' => true,
        ]);
        $master->services()->attach($secondService->id, ['duration_minutes' => 35, 'price' => 30]);

        Sanctum::actingAs($client);

        $create = $this->postJson('/api/appointments', [
            'master_id' => $master->id,
            'service_ids' => [$service->id, $secondService->id],
            'start_at' => now()->addDay()->setTime(14, 0)->toDateTimeString(),
            'source' => 'site',
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.services.0.id', $service->id)
            ->assertJsonPath('data.services.1.id', $secondService->id);

        $appointmentId = (int) $create->json('data.id');
        $this->assertDatabaseCount('appointment_service', 2);
        $this->assertDatabaseHas('appointment_service', [
            'appointment_id' => $appointmentId,
            'service_id' => $service->id,
        ]);
        $this->assertDatabaseHas('appointment_service', [
            'appointment_id' => $appointmentId,
            'service_id' => $secondService->id,
        ]);
    }

    public function test_client_can_create_order_with_multiple_lines(): void
    {
        [$client, $master, $service] = $this->createCatalogContext();

        $secondService = Service::query()->create([
            'category_id' => $service->category_id,
            'name' => 'Styling 2',
            'slug' => 'styling-2',
            'duration_minutes' => 30,
            'price_from' => 25,
            'price_to' => 40,
            'sort' => 21,
            'is_active' => true,
        ]);
        $master->services()->attach($secondService->id, ['duration_minutes' => 30, 'price' => 25]);

        Sanctum::actingAs($client);

        $create = $this->postJson('/api/appointments', [
            'source' => 'site',
            'comment' => 'Bundle booking',
            'lines' => [
                [
                    'start_at' => now()->addDays(2)->setTime(10, 0)->toDateTimeString(),
                    'service_ids' => [$service->id],
                    'master_id' => $master->id,
                ],
                [
                    'start_at' => now()->addDays(3)->setTime(12, 0)->toDateTimeString(),
                    'service_ids' => [$secondService->id],
                    'master_id' => $master->id,
                ],
            ],
        ]);

        $create->assertCreated()
            ->assertJsonPath('booking_order.comment', 'Bundle booking')
            ->assertJsonPath('booking_order.appointments_count', 2);

        $orderId = (int) $create->json('booking_order.id');
        $this->assertGreaterThan(0, $orderId);

        $this->assertDatabaseHas('booking_orders', [
            'id' => $orderId,
            'client_id' => $client->id,
            'source' => 'site',
        ]);
        $this->assertDatabaseCount('appointments', 2);
        $this->assertDatabaseHas('appointments', [
            'booking_order_id' => $orderId,
            'service_id' => $service->id,
        ]);
        $this->assertDatabaseHas('appointments', [
            'booking_order_id' => $orderId,
            'service_id' => $secondService->id,
        ]);
    }

    /** @return array{User, Master, Service} */
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

        $master = Master::query()->create([
            'user_id' => $masterUser->id,
            'name' => 'Master A',
            'slug' => 'master-a',
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

        return [$client, $master, $service];
    }
}
