<?php

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Desa::create([
            'daerah_id' => 1,
            'nm_desa' => 'Klari'
        ]);
        Desa::create([
            'daerah_id' => 1,
            'nm_desa' => 'Lemah Mulya'
        ]);
        Desa::create([
            'daerah_id' => 1,
            'nm_desa' => 'Adiarsa Timur'
        ]);
        Desa::create([
            'daerah_id' => 1,
            'nm_desa' => 'Tunggak Jati'
        ]);
    }
}
