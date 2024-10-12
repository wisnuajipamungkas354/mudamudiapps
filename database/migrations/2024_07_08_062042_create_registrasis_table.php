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
        Schema::create('registrasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daerah_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('desa_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('kelompok_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('nama');
            $table->enum('jk', ['L', 'P']);
            $table->string('kota_lahir');
            $table->date('tgl_lahir');
            $table->enum('mubaligh', ['Ya', 'Bukan']);
            $table->string('status');
            $table->text('detail_status');
            $table->integer('usia');
            $table->enum('siap_nikah', ['Siap', 'Belum']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_data');
    }
};
