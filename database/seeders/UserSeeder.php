<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Owner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@vetclinic.com',
            'password' => Hash::make('admin123'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'phone' => '+1234567890',
            'is_active' => true,
        ]);

        // Create Doctor
        $doctor = User::create([
            'name' => 'Dr. John Smith',
            'email' => 'doctor@vetclinic.com',
            'password' => Hash::make('doctor123'),
            'role_id' => Role::where('name', 'Doctor')->first()->id,
            'phone' => '+1234567891',
            'is_active' => true,
        ]);

        // Create Receptionist
        $receptionist = User::create([
            'name' => 'Jane Doe',
            'email' => 'receptionist@vetclinic.com',
            'password' => Hash::make('receptionist123'),
            'role_id' => Role::where('name', 'Receptionist')->first()->id,
            'phone' => '+1234567892',
            'is_active' => true,
        ]);

        // Create Pet Owner
        $petOwner = User::create([
            'name' => 'Mike Johnson',
            'email' => 'owner@example.com',
            'password' => Hash::make('owner123'),
            'role_id' => Role::where('name', 'Pet Owner')->first()->id,
            'phone' => '+1234567893',
            'address' => '123 Main St, City, State',
            'is_active' => true,
        ]);

        // Create Owner record for pet owner
        Owner::create([
            'user_id' => $petOwner->id,
            'emergency_contact' => '+1234567894',
            'notes' => 'Prefers morning appointments',
        ]);
    }
}