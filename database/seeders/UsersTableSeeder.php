<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Room;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User;
        $user->username = "ST";
        $user->email = "sam@tudberry.net";
        $user->email_verified_at = now();
        $user->password = "password0";
        $user->guest = false;
        $user->remember_token = Str::random(10);
        $user->save();

        $user = new User;
        $user->username = "user1205";
        $user->email = "joe1205@hotmail.com";
        $user->email_verified_at = now();
        $user->password = "password1";
        $user->guest = false;
        $user->remember_token = Str::random(10);
        $user->save();

        //$user->roles()->updateExistingPivot($role->id, ['room_id' => $room->id]);

        $user = new User;
        $user->username = "mike999";
        $user->email = "mike999@gmail.com";
        $user->email_verified_at = now();
        $user->password = "password2";
        $user->guest = false;
        $user->remember_token = Str::random(10);
        $user->save();

        $user = new User;
        $user->username = "baduser";
        $user->email = "baduser@hotmail.com";
        $user->email_verified_at = now();
        $user->password = "password3";
        $user->guest = false;
        $user->remember_token = Str::random(10);  
        $user->save();

        $user = new User;
        $user->username = "kait092";
        $user->email = "kait092@hotmail.com";
        $user->email_verified_at = now();
        $user->password = "password4";
        $user->guest = false;
        $user->remember_token = Str::random(10);  
        $user->save();

        User::factory()->has(\App\Models\Profile::factory())->count(30)->create();
    }
}
