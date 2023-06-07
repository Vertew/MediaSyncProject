<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User;
        $user->username = "user1205";
        $user->email = "joe1205@hotmail.com";
        $user->email_verified_at = now();
        $user->password = "password1";
        $user->remember_token = Str::random(10);  
        $user->save();


        $user = new User;
        $user->username = "mike999";
        $user->email = "mike999@gmail.com";
        $user->email_verified_at = now();
        $user->password = "password2";
        $user->remember_token = Str::random(10);
        $user->save();

        $user = new User;
        $user->username = "baduser";
        $user->email = "baduser@hotmail.com";
        $user->email_verified_at = now();
        $user->password = "password3";
        $user->remember_token = Str::random(10);  
        $user->save();

        $user = new User;
        $user->username = "kait092";
        $user->email = "kait092@hotmail.com";
        $user->email_verified_at = now();
        $user->password = "password4";
        $user->remember_token = Str::random(10);  
        $user->save();

        User::factory()->count(30)->create();
    }
}
