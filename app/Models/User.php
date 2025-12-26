<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'role',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleEnum::class
        ];
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function becomeDoctor(array $doctor_data)
    {
        $doctor = Doctor::create(array_merge(
            $doctor_data,
            ['user_id' => $this->id]
        ));

        $this->update([
            'role' => 'doctor'
        ]);

        return $doctor;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversationsAsDoctor(): HasMany
    {
        return $this->doctor
            ? $this->doctor->conversations()
            : Conversation::whereRaw('0 = 1');
    }

    public function conversationsAsUser(): HasMany
    {
        return $this->hasMany(Conversation::class, 'user_id');
    }

    public function conversations(): HasMany|Builder
    {
        return $this->role->value === 'doctor'
            ? $this->conversationsAsDoctor()
            : $this->conversationsAsUser();
    }
}
