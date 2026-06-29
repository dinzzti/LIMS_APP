<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Uncomment jika ingin buat 10 user
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Laboran LIMS',
            'email' => 'laboran@lims.id',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Supervisor LIMS',
            'email' => 'supervisor@lims.id',
            'password' => bcrypt('password'),
        ]);

        // Jalankan SampleSeeder
        $this->call(SampleSeeder::class);
    }
}
