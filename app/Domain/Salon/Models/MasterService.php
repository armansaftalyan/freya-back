<?php

declare(strict_types=1);

namespace App\Domain\Salon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterService extends Model
{
    protected $table = 'master_service';

    protected $fillable = [
        'master_id',
        'service_id',
        'duration_minutes',
        'price',
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
