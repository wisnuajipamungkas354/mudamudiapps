<?php

namespace Database\Seeders;

use App\Models\Dapukan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DapukanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dapukan::create([
            'tingkatan' => 'Daerah',
            'nama_dapukan' => 'KI',
        ]);

        Dapukan::create([
            'tingkatan' => 'Daerah',
            'nama_dapukan' => 'Wakil KI',
        ]);
        
        Dapukan::create([
            'tingkatan' => 'Daerah',
            'nama_dapukan' => 'Penerobos',
        ]);
        
        Dapukan::create([
            'tingkatan' => 'Daerah',
            'nama_dapukan' => 'KU',
        ]);

        Dapukan::create([
            'tingkatan' => 'Daerah',
            'nama_dapukan' => 'Muballigh',
        ]);
    }
}
