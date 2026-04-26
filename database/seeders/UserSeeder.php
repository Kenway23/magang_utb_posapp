<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $owner = DB::table('roles')->where('nama_role', 'owner')->first();

        DB::table('users')->updateOrInsert(
            ['username' => 'owner'],
            [
                'name' => 'Owner',
                'password' => Hash::make('123456'),
                'role_id' => $owner->role_id
            ]
        );
    }
}