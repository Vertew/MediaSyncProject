<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Room;
use App\Models\User;
use App\Models\Role;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $role = Role::find(1);
        $user = User::find(2);

        $room = new Room;
        $room->user_id = $user->id;
        $room->name = "Joe's Room";
        $room->key = Str::random(16);
        $room->save();


        $user->roles()->attach($role, ['room_id' => $room->id]);
    }
}
