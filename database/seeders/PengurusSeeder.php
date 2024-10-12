<?php

namespace Database\Seeders;

use App\Models\Pengurus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengurusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengurus::create([
            'nm_pengurus' => 'Dinda Febrianur Rosyid',
            'jk' => 'L',
            'dapukan' => 'Ketua',
            'role' => 'MM Daerah',
            'nm_tingkatan' => 'Karawang Timur',
            'no_hp' => '085258346949'
        ]);
        Pengurus::create([
            'nm_pengurus' => 'Hifdi Jaya Amirudin',
            'jk' => 'L',
            'dapukan' => 'Wakil Ketua',
            'role' => 'MM Daerah',
            'nm_tingkatan' => 'Karawang Timur',
            'no_hp' => '085714509637'
        ]);
        Pengurus::create([
            'nm_pengurus' => 'Wisnu Aji Pamungkas',
            'jk' => 'L',
            'dapukan' => 'Wakil Ketua',
            'role' => 'MM Daerah',
            'nm_tingkatan' => 'Karawang Timur',
            'no_hp' => '085889634432'
        ]);
        
    }
}
