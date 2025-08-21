<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([RoleSeeder::class,]);

        $adminrole = Role::where('role','admin')->first();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin1234'),
            'role_id' => $adminrole->id
        ]);
    }
}
