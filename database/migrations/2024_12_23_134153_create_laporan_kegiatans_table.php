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
        Schema::create('laporan_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan_id');
            $table->foreign('kegiatan_id')->references('id')->on('kegiatans')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('daerah_id')->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('desa_id')->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('kelompok_id')->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('hadir_l');
            $table->integer('hadir_p');
            $table->integer('izin_l');
            $table->integer('izin_p');
            $table->integer('alfa_l');
            $table->integer('alfa_p');
            $table->integer('total_peserta');
            $table->integer('in_time');
            $table->integer('on_time');
            $table->integer('over_time');
            $table->integer('tidak_datang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kegiatans');
    }
};
