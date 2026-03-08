<?php

declare(strict_types=1);

namespace App\Domain\Salon\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Master extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'name_i18n',
        'slug',
        'bio',
        'bio_i18n',
        'avatar',
        'experience_years',
        'specialties',
        'specialties_i18n',
        'languages',
        'certificates',
        'certificates_i18n',
        'instagram',
        'schedule_rules',
        'sort',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'schedule_rules' => 'array',
            'specialties' => 'array',
            'specialties_i18n' => 'array',
            'languages' => 'array',
            'certificates' => 'array',
            'certificates_i18n' => 'array',
            'name_i18n' => 'array',
            'bio_i18n' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot(['duration_minutes', 'price'])
            ->withTimestamps();
    }

    public function masterServices(): HasMany
    {
        return $this->hasMany(MasterService::class, 'master_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
