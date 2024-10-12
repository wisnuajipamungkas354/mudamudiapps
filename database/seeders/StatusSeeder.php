<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Status
        Status::create([
            'nm_status' => 'Pelajar SMP',
        ]);
        Status::create([
            'nm_status' => 'Pelajar SMA',
        ]);
        Status::create([
            'nm_status' => 'Pelajar SMK',
        ]);
        Status::create([
            'nm_status' => 'Mahasiswa D3',
        ]);
        Status::create([
            'nm_status' => 'Mahasiswa S1/D4',
        ]);

        Status::create([
            'nm_status' => 'Mahasiswa S2',
        ]);
        Status::create([
            'nm_status' => 'Mahasiswa S3',
        ]);
        Status::create([
            'nm_status' => 'Pencari Kerja SMP',
        ]);
        Status::create([
            'nm_status' => 'Pencari Kerja SMA/K',
        ]);

        Status::create([
            'nm_status' => 'Pencari Kerja D3',
        ]);
        Status::create([
            'nm_status' => 'Pencari Kerja S1/D4',
        ]);
        Status::create([
            'nm_status' => 'Pencari Kerja S2',
        ]);
        Status::create([
            'nm_status' => 'Pencari Kerja S3',
        ]);
        Status::create([
            'nm_status' => 'Karyawan/Pegawai',
        ]);
        Status::create([
            'nm_status' => 'Tenaga Sabilillah (SB)',
        ]);
        Status::create([
            'nm_status' => 'Kuliah & Kerja',
        ]);
        Status::create([
            'nm_status' => 'Wirausaha/Freelance',
        ]);
    }
}
