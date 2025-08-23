<?php
namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Admin', 'description' => 'Full system access'],
            ['name' => 'Doctor', 'description' => 'Medical professional with patient access'],
            ['name' => 'Receptionist', 'description' => 'Front desk operations'],
            ['name' => 'Pet Owner', 'description' => 'Pet owner with limited access'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}