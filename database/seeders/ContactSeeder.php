<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where("username", "Tian")->first();
        Contact::create([
            "first_name" => "Chris",
            "last_name" => "Tian",
            "email" => "test@gmail.com",
            "phone" => "081234567",
            "user_id" => $user->id
        ]);
    }
}
