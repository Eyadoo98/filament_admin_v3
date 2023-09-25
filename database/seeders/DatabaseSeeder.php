<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        if ($this->command->confirm('Do you want to refresh the database?')) {

            $this->command->call('migrate:refresh');
            $this->call(
                [
                    CountrySeeder::class,
                    StateSeeder::class,
                    CitySeeder::class,
                    UserSeeder::class,
                ]
            );
            $this->command->info('Database was refreshed');
        }else{
            die('Refresh aborted!');
        }
    }
}
