<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'user_id',
        'appointment_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id')
            ->join('doctors', 'users.id', '=', 'doctors.user_id');
    }

    public function getDoctorUserAttribute()
    {
        return $this->doctor->user ?? null;
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }


    public function getOtherUser(int $current_user_id): \App\Models\User
    {
        if (
            !$this->relationLoaded('doctor') ||
            !($this->doctor?->relationLoaded('user')) ||
            !$this->relationLoaded('user')
        ) {
            $this->load(['user', 'doctor.user']);
        }

        if ($this->user_id === $current_user_id) {
            return $this->doctor?->user;
        }

        return $this->user;
    }
}
