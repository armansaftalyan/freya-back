<?php

declare(strict_types=1);

namespace App\Domain\Salon\Models;

use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'booking_order_id',
        'client_id',
        'master_id',
        'service_id',
        'start_at',
        'end_at',
        'status',
        'comment',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'status' => AppointmentStatus::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function bookingOrder(): BelongsTo
    {
        return $this->belongsTo(BookingOrder::class, 'booking_order_id');
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot(['duration_minutes', 'price', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function getTotalPriceAttribute(): float
    {
        /** @var Collection<int, Service> $services */
        $services = $this->relationLoaded('services')
            ? $this->services
            : $this->services()->get();

        if ($services->isEmpty()) {
            return (float) ($this->service?->price_from ?? 0);
        }

        $sum = $services->sum(function (Service $service): float {
            $pivotPrice = $service->pivot?->price;
            return (float) ($pivotPrice ?? $service->price_from ?? 0);
        });

        return round((float) $sum, 2);
    }
}
