<?php

declare(strict_types=1);

namespace App\Observers;

use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\BookingOrder;

class AppointmentObserver
{
    public function saved(Appointment $appointment): void
    {
        $this->syncOrder($appointment);
    }

    public function deleted(Appointment $appointment): void
    {
        $this->syncOrder($appointment);
    }

    private function syncOrder(Appointment $appointment): void
    {
        $orderId = (int) ($appointment->booking_order_id ?? 0);
        if ($orderId <= 0) {
            return;
        }

        $order = BookingOrder::query()->find($orderId);
        if ($order === null) {
            return;
        }

        $order->refreshAggregates();
        $order->save();
    }
}
