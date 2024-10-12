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
            $table->id();
            $table->string('nm_kegiatan');
            $table->string('tempat_kegiatan');
            $table->dateTime('waktu_pelaksanaan');
            $table->string('asal_data_peserta');
            $table->string('tingkatan_kegiatan');
            $table->string('detail_tingkatan');
            $table->string('kategori_peserta');
            $table->boolean('is_sesi')->default(false);
            $table->integer('jml_sesi')->default(1);
            $table->integer('sesi_aktif')->default(1);
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
