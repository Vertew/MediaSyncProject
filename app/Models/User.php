<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'private.user.'.$this->id;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function profile(): HasOne{
        return $this->hasOne(Profile::class);
    }

    public function videos(): HasMany{
        return $this->hasMany(Video::class);
    }

    public function files(): HasMany{
        return $this->hasMany(File::class);
    }

    public function rooms(): HasMany{
        return $this->hasMany(Room::class);
    }

    public function roles(): BelongsToMany{
        return $this->belongsToMany(Role::class)->withPivot('room_id');
    }

    public function friends(): BelongsToMany{
        return $this->belongsToMany(User::class, 'user_user', 'user1_id', 'user2_id');
    }

    public function banned_from(): BelongsToMany{
        return $this->belongsToMany(Room::class);
    }
}
