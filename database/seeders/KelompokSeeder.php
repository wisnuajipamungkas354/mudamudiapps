<?php

namespace Database\Seeders;

use App\Models\Kelompok;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelompokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kelompok::create([
            'desa_id' => 1,
            'nm_kelompok' => 'Cirejag'
        ]);
        Kelompok::create([
            'desa_id' => 1,
            'nm_kelompok' => 'Belendung'
        ]);
        Kelompok::create([
            'desa_id' => 1,
            'nm_kelompok' => 'Cibalongsari 1'
        ]);
        Kelompok::create([
            'desa_id' => 1,
            'nm_kelompok' => 'Cibalongsari 2'
        ]);
        Kelompok::create([
            'desa_id' => 1,
            'nm_kelompok' => 'Pancawati'
        ]);
        Kelompok::create([
            'desa_id' => 1,
            'nm_kelompok' => 'Kalimulya'
        ]);
        Kelompok::create([
            'desa_id' => 2,
            'nm_kelompok' => 'Tamiang 1'
        ]);
        Kelompok::create([
            'desa_id' => 2,
            'nm_kelompok' => 'Tamiang 2'
        ]);
        Kelompok::create([
            'desa_id' => 2,
            'nm_kelompok' => 'CKM'
        ]);
        Kelompok::create([
            'desa_id' => 2,
            'nm_kelompok' => 'Tipar'
        ]);
        Kelompok::create([
            'desa_id' => 2,
            'nm_kelompok' => 'Anggadita'
        ]);
        Kelompok::create([
            'desa_id' => 2,
            'nm_kelompok' => 'HNH'
        ]);
        Kelompok::create([
            'desa_id' => 3,
            'nm_kelompok' => 'Karawang Kota 1'
        ]);
        Kelompok::create([
            'desa_id' => 3,
            'nm_kelompok' => 'Karawang Kota 2'
        ]);
        Kelompok::create([
            'desa_id' => 3,
            'nm_kelompok' => 'Babakan Jati'
        ]);
        Kelompok::create([
            'desa_id' => 3,
            'nm_kelompok' => 'Wirasaba'
        ]);
        Kelompok::create([
            'desa_id' => 3,
            'nm_kelompok' => 'Wadas'
        ]);
        Kelompok::create([
            'desa_id' => 4,
            'nm_kelompok' => 'Tanjung Baru Barat'
        ]);
        Kelompok::create([
            'desa_id' => 4,
            'nm_kelompok' => 'Tanjung Baru Timur'
        ]);
        Kelompok::create([
            'desa_id' => 4,
            'nm_kelompok' => 'Tunggak Jati'
        ]);
        Kelompok::create([
            'desa_id' => 4,
            'nm_kelompok' => 'Kalangsuria'
        ]);
        Kelompok::create([
            'desa_id' => 4,
            'nm_kelompok' => 'Pakis Jaya'
        ]);
    }
}
