<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "username" => "Tian",
            "password" => Hash::make("r4h4s!a"),
            "name" => "Christian",
            "token" => "token"
        ]);
        User::create([
            "username" => "Budi",
            "password" => Hash::make("k3r3n!?"),
            "name" => "Budi Kafka",
            "token" => "keren"
        ]);
    }
}
