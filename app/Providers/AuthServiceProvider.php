<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin-action', function (User $user, int $room_id) {
            return $user->roles->where('pivot.room_id', $room_id)->contains(1);
        });

        Gate::define('moderator-action', function (User $user, int $room_id) {
            return $user->roles->where('pivot.room_id', $room_id)->where('id', '<', 3)->isNotEmpty();
        });

        Gate::define('standard-action', function (User $user, int $room_id) {
            return $user->roles->where('pivot.room_id', $room_id)->where('id', '<', 4)->isNotEmpty();
        });

        Gate::define('full-account', function (User $user) {
            return !($user->guest);
        });

        Gate::define('private', function (User $user, int $id) {
            return $user->id == $id;
        });
    }
}
