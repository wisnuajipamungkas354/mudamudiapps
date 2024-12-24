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
            $table->string('kategori_peserta');
            $table->string('detail_kategori')->nullable();
            $table->string('kode_kegiatan', 6);
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
