<?php

namespace App\Models;

use App\Enums\SpecialtyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin IdeHelperDoctor
 */
class Doctor extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty',
        'phone',
        'address',
        'bio',
        'work_from',
        'work_to',
        'user_id',
        'image',
        'license',
        'degree',
        'ticket_price',
        'active'
    ];

    protected function casts(): array
    {
        return [
            'specialty' => SpecialtyEnum::class,
            'active' => 'boolean',
            'work_from' => 'datetime',
            'work_to' => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
    public function getNameAttribute(): ?string
    {
        return $this->user?->name;
    }

    public static function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when($filters['name'] ?? null, function ($query, $name) {
            $query->whereHas('user', function ($q) use ($name) {
                $q->where('name', 'LIKE', '%' . $name . '%');
            });
        })
            ->when($filters['address'] ?? null, function ($query, $address) {
                $query->where('address', 'LIKE', '%' . $address . '%');
            })
            ->when($filters['specialty'] ?? null, function ($query, $specialty) {
                $query->where('specialty', $specialty);
            });
    }
}
