<?php

namespace Database\Seeders;

use App\Models\Mudamudi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateMudaMudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mmKlari = Mudamudi::query()->where('desa_id', '1')->get();
        foreach($mmKlari as $index => $item) {
            $yearMonth = date('ym', mktime(0,0,0,10,0,2024));
            $uniqueId = str_pad($index + 1, 4, "0", STR_PAD_LEFT);
            $item->id = $yearMonth . $uniqueId;
            $item->save();
        }

        $mmLemahMulya = Mudamudi::query()->where('desa_id', '2')->get();
        foreach($mmLemahMulya as $index => $item) {
            $yearMonth = date('ym', mktime(0,0,0,11,0,2024));
            $uniqueId = str_pad($index + 1, 4, "0", STR_PAD_LEFT);
            $item->id = $yearMonth . $uniqueId;
            $item->save();
        }

        $mmAdiarsaTimur = Mudamudi::query()->where('desa_id', '3')->get();
        foreach($mmAdiarsaTimur as $index => $item) {
            $yearMonth = date('ym', mktime(0,0,0,12,0,2024));
            $uniqueId = str_pad($index + 1, 4, "0", STR_PAD_LEFT);
            $item->id = $yearMonth . $uniqueId;
            $item->save();
        }

        $mmTunggakJati = Mudamudi::query()->where('desa_id', '4')->get();
        foreach($mmTunggakJati as $index => $item) {
            $yearMonth = date('ym', mktime(0,0,0,13,0,2024));
            $uniqueId = str_pad($index + 1, 4, "0", STR_PAD_LEFT);
            $item->id = $yearMonth . $uniqueId;
            $item->save();
        }
    }
}
