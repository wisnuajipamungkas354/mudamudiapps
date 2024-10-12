<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mudamudi>
 */
class MudamudiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daerah_id' => $this->faker->numberBetween(1, 1),
            'desa_id' => $this->faker->numberBetween(1, 4),
            'kelompok_id' => $this->faker->numberBetween(1, 6),
            'nama' => $this->faker->name(),
            'jk' => $this->faker->randomElement(['L', 'P']),
            'kota_lahir' => $this->faker->city(),
            'tgl_lahir' => $this->faker->date('Y-m-d', '2012-01-01'),
            'mubaligh' => $this->faker->randomElement(['Ya', 'Bukan']),
            'status' => $this->faker->randomElement(['Pelajar SMP', 'Pelajar SMA', 'Pelajar SMK', 'Mahasiswa D3', 'Mahasiswa D4', 'Mahasiswa S1', 'Mahasiswa S2', 'Mahasiswa S3', 'Pencari Kerja SMP', 'Pencari Kerja SMA', 'Pencari Kerja SMK', 'Pencari Kerja D3', 'Pencari Kerja D4', 'Pencari Kerja S1', 'Pencari Kerja S2', 'Pencari Kerja S3', 'Karyawan PT', 'Tenaga Sabilillah (SB)', 'Kuliah & Kerja', 'Wirausaha']),
            'detail_status' => $this->faker->randomElement(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h']),
            'siap_nikah' => $this->faker->randomElement(['Siap', 'Belum']),
            'usia' => $this->faker->numberBetween(12, 30),
        ];
    }
}
