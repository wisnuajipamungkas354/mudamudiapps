<?php

namespace Database\Seeders;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use App\Models\Status;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            DaerahSeeder::class,
            DesaSeeder::class,
            KelompokSeeder::class,
            StatusSeeder::class,
            PengurusSeeder::class,
            RegistrasiSeeder::class,
        ]);
    }
}
