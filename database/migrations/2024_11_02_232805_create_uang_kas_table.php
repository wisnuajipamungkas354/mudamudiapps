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
        Schema::create('uang_kas', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('tingkatan');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->string('nm_penginput');
            $table->enum('jenis_kas', ['Pemasukan', 'Pengeluaran', 'Saldo Awal']);
            $table->integer('nominal');
            $table->text('keterangan');
            $table->date('tgl');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_kas');
    }
};
