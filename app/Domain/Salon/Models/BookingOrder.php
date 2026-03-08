<?php

declare(strict_types=1);

namespace App\Domain\Salon\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'start_at',
        'end_at',
        'status',
        'total_price',
        'comment',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'booking_order_id');
    }

    public function refreshAggregates(): void
    {
        $appointments = $this->appointments()
            ->with(['service', 'services'])
            ->orderBy('start_at')
            ->get();

        if ($appointments->isEmpty()) {
            $this->start_at = null;
            $this->end_at = null;
            $this->status = 'pending';
            $this->total_price = 0;
            return;
        }

        $statuses = $appointments
            ->map(fn (Appointment $appointment): string => $appointment->status->value)
            ->unique()
            ->values();

        $this->start_at = $appointments->min('start_at');
        $this->end_at = $appointments->max('end_at');
        $this->status = $statuses->count() === 1 ? (string) $statuses->first() : 'mixed';
        $this->total_price = round((float) $appointments->sum('total_price'), 2);
    }
}
