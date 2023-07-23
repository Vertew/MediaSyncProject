<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = new Role;
        $role->role = "Admin";
        $role->save();

        $role = new Role;
        $role->role = "Moderator";
        $role->save();

        $role = new Role;
        $role->role = "Standard";
        $role->save();

        $role = new Role;
        $role->role = "Restricted";
        $role->save();
    }
}
