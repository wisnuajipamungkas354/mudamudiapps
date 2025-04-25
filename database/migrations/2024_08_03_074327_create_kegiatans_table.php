<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nm_kegiatan');
            $table->string('tempat_kegiatan');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->string('tingkatan_kegiatan');
            $table->string('detail_tingkatan');
            // Informasi Peserta
            $table->enum('jk_peserta', ['LP', 'L', 'P']); // Filter peserta berdasarkan jenis kelamin
            $table->json('kategori_peserta'); // Pelajar SMP, SMA/K, Lepas Pelajar, Karyawan/Pegawai, Wirausaha/Freelance, Mahasiswa, Pencari Kerja
            $table->boolean('siap_nikah')->default(false);
            $table->boolean('konfirmasi_kehadiran')->default(false);
            $table->string('kode_kegiatan', 4)->nullable();
            $table->boolean('is_finish')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegitans');
    }
};
