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
        Schema::create('dapukans', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkatan', ['Daerah', 'Desa', 'Kelompok']);
            $table->string('nama_dapukan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dapukans');
    }
};
