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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan_id');
            $table->foreign('kegiatan_id')->references('id')->on('kegiatans')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('mudamudi_id')->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('keterangan')->default('Alfa');
            $table->string('kedatangan')->default('Tidak Datang');
            $table->string('kategori_izin')->nullable(); // Kerja, Kuliah, Sekolah, Acara Keluarga, Acara Mendesak
            $table->text('ket_izin')->nullable(); // 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
