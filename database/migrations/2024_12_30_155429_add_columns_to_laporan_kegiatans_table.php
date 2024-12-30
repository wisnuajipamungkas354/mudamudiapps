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
        Schema::table('laporan_kegiatans', function (Blueprint $table) {
            $table->addColumn('integer', 'sakit');
            $table->addColumn('integer', 'kerja');
            $table->addColumn('integer', 'kuliah');
            $table->addColumn('integer', 'sekolah');
            $table->addColumn('integer', 'acara_keluarga');
            $table->addColumn('integer', 'acara_mendesak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_kegiatans', function (Blueprint $table) {
            $table->dropColumn(['sakit', 'kerja', 'kuliah', 'sekolah', 'acara_keluarga', 'acara_mendesak']);
        });
    }
};
