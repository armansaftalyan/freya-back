<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Branch;
use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'phone' => '+10000000001',
                'password' => 'password',
            ]
        );
        $admin->syncRoles(['admin']);

        $manager = User::query()->updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Manager User',
                'phone' => '+10000000002',
                'password' => 'password',
            ]
        );
        $manager->syncRoles(['manager']);

        $masterUser = User::query()->updateOrCreate(
            ['email' => 'master@gmail.com'],
            [
                'name' => 'Master User',
                'phone' => '+10000000003',
                'password' => 'password',
            ]
        );
        $masterUser->syncRoles(['master']);

        $client = User::query()->updateOrCreate(
            ['email' => 'client@gmail.com'],
            [
                'name' => 'Client User',
                'phone' => '+10000000004',
                'password' => 'password',
            ]
        );
        $client->syncRoles(['client']);

        $categoryHair = Category::query()->firstOrCreate(
            ['slug' => 'hair'],
            ['name' => 'Hair', 'sort' => 10, 'is_active' => true]
        );

        $categoryNails = Category::query()->firstOrCreate(
            ['slug' => 'nails'],
            ['name' => 'Nails', 'sort' => 20, 'is_active' => true]
        );

        $serviceCut = Service::query()->firstOrCreate(
            ['slug' => 'women-haircut'],
            [
                'category_id' => $categoryHair->id,
                'name' => 'Women Haircut',
                'description' => 'Haircut and styling',
                'duration_minutes' => 60,
                'price_from' => 40,
                'price_to' => 70,
                'sort' => 10,
                'is_active' => true,
            ]
        );

        $serviceManicure = Service::query()->firstOrCreate(
            ['slug' => 'manicure-classic'],
            [
                'category_id' => $categoryNails->id,
                'name' => 'Classic Manicure',
                'description' => 'Nail care and polish',
                'duration_minutes' => 50,
                'price_from' => 30,
                'price_to' => 55,
                'sort' => 20,
                'is_active' => true,
            ]
        );

        $workingHours = [
            'monday' => [['start' => '09:00', 'end' => '19:00']],
            'tuesday' => [['start' => '09:00', 'end' => '19:00']],
            'wednesday' => [['start' => '09:00', 'end' => '19:00']],
            'thursday' => [['start' => '09:00', 'end' => '19:00']],
            'friday' => [['start' => '09:00', 'end' => '19:00']],
            'saturday' => [['start' => '10:00', 'end' => '17:00']],
            'sunday' => [],
        ];

        $branch = Branch::query()->firstOrCreate(
            ['name' => 'Central Branch'],
            [
                'address' => 'Main street 10',
                'phone' => '+10000000010',
                'geo_lat' => 40.71280000,
                'geo_lng' => -74.00600000,
                'working_hours' => $workingHours,
                'is_active' => true,
            ]
        );

        $master = Master::query()->firstOrCreate(
            ['name' => 'Anna Master'],
            [
                'user_id' => $masterUser->id,
                'bio' => 'Senior beauty specialist',
                'is_active' => true,
                'sort' => 10,
                'schedule_rules' => $workingHours,
            ]
        );

        $master->services()->syncWithoutDetaching([
            $serviceCut->id => ['duration_minutes' => 60, 'price' => 50],
            $serviceManicure->id => ['duration_minutes' => 50, 'price' => 40],
        ]);

        $startPending = Carbon::now()->addDays(1)->setTime(11, 0);
        Appointment::query()->firstOrCreate(
            [
                'client_id' => $client->id,
                'master_id' => $master->id,
                'branch_id' => $branch->id,
                'service_id' => $serviceCut->id,
                'start_at' => $startPending,
            ],
            [
                'end_at' => $startPending->copy()->addMinutes($serviceCut->duration_minutes),
                'status' => AppointmentStatus::Pending,
                'source' => 'site',
            ]
        );

        $startConfirmed = Carbon::now()->addDays(2)->setTime(12, 0);
        Appointment::query()->firstOrCreate(
            [
                'client_id' => $client->id,
                'master_id' => $master->id,
                'branch_id' => $branch->id,
                'service_id' => $serviceManicure->id,
                'start_at' => $startConfirmed,
            ],
            [
                'end_at' => $startConfirmed->copy()->addMinutes($serviceManicure->duration_minutes),
                'status' => AppointmentStatus::Confirmed,
                'source' => 'instagram',
            ]
        );
    }
}
