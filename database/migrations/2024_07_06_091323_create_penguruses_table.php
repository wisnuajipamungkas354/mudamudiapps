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
        Schema::create('penguruses', function (Blueprint $table) {
            $table->id();
            $table->string('nm_pengurus');
            $table->enum('jk', ['L', 'P']);
            $table->enum('dapukan', [
                'Ketua',
                'Wakil Ketua',
                'Penerobos',
                'Sekretaris',
                'Bendahara',
                'Keputrian',
                'Seksi-Seksi'
            ]);
            $table->string('role');
            $table->string('nm_tingkatan');
            $table->string('no_hp', 16);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penguruses');
    }
};
