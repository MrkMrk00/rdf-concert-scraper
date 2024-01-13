<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         $me = \App\Models\User::factory()->create([
             'name' => 'Marek',
             'email' => 'mhau@outlook.cz',
             'password' => \Hash::make('123456'),
         ]);

         Resource::factory()->create([
             'src' => 'https://www.redutajazzclub.cz/program-en',
             'name' => 'Reduta Jazz Club',
             'id_user' => $me->id,
         ]);

//        Resource::factory()->create([
//            'src' => 'https://www.jazzdock.cz/cs/program',
//            'name' => 'Jazz Dock',
//            'id_user' => $me->id,
//        ]);
    }
}
