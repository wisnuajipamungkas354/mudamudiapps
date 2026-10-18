<?php

namespace Tests\Unit;

use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use App\Services\RekapKegiatanExportService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class RekapKegiatanExportServiceTest extends TestCase
{
    public function test_can_instantiate_service_and_methods_exist(): void
    {
        $this->assertTrue(method_exists(RekapKegiatanExportService::class, 'export'));
        $this->assertTrue(method_exists(RekapKegiatanExportService::class, 'getRecords'));
    }

    public function test_xlsx_and_csv_generation(): void
    {
        $laporan = new LaporanKegiatan();
        $laporan->id = 1;
        $laporan->kegiatan_id = 'keg-001';
        $laporan->tingkatan_laporan = 'MM Desa';
        $laporan->detail_tingkatan = 'Desa Bojongsoang';
        $laporan->hadir_l = 10;
        $laporan->hadir_p = 15;
        $laporan->alfa_l = 2;
        $laporan->alfa_p = 3;
        $laporan->total_peserta = 30;

        $kegiatan = new Kegiatan();
        $kegiatan->id = 'keg-001';
        $kegiatan->nm_kegiatan = 'Kajian Rutin Pemuda';
        $kegiatan->waktu_mulai = '2026-10-18 19:30:00';

        $laporan->setRelation('kegiatan', $kegiatan);

        $tempXlsx = tempnam(sys_get_temp_dir(), 'test_') . '.xlsx';
        $tempCsv = tempnam(sys_get_temp_dir(), 'test_') . '.csv';

        // Use reflection to invoke protected generateXlsx and generateCsv
        $reflector = new \ReflectionClass(RekapKegiatanExportService::class);

        $generateXlsx = $reflector->getMethod('generateXlsx');
        $generateXlsx->setAccessible(true);
        $generateXlsx->invoke(null, $tempXlsx, $laporan, new Collection([]), 'Hadir');

        $this->assertFileExists($tempXlsx);
        $this->assertGreaterThan(0, filesize($tempXlsx));

        $generateCsv = $reflector->getMethod('generateCsv');
        $generateCsv->setAccessible(true);
        $generateCsv->invoke(null, $tempCsv, new Collection([]));

        $this->assertFileExists($tempCsv);
        $this->assertGreaterThan(0, filesize($tempCsv));

        // Clean up
        @unlink($tempXlsx);
        @unlink($tempCsv);
    }

    public function test_format_row_data(): void
    {
        $kelompok = new \App\Models\Kelompok();
        $kelompok->nm_kelompok = 'Kelompok Alpha';

        $desa = new \App\Models\Desa();
        $desa->nm_desa = 'Desa Bojongsoang';

        $daerah = new \App\Models\Daerah();
        $daerah->nm_daerah = 'Daerah Bandung Selatan';

        $mudamudi = new \App\Models\Mudamudi();
        $mudamudi->nama = 'Ahmad Fulan';
        $mudamudi->jk = 'L';
        $mudamudi->status = 'Mahasiswa';
        $mudamudi->usia = 21;
        $mudamudi->setRelation('kelompok', $kelompok);
        $mudamudi->setRelation('desa', $desa);
        $mudamudi->setRelation('daerah', $daerah);

        $presensi = new \App\Models\Presensi();
        $presensi->setRawAttributes([
            'keterangan' => 'Hadir',
            'kedatangan' => 'In Time',
            'updated_at' => '2026-10-18 19:15:00',
        ]);
        $presensi->setRelation('mudamudi', $mudamudi);

        $reflector = new \ReflectionClass(RekapKegiatanExportService::class);
        $formatRowData = $reflector->getMethod('formatRowData');
        $formatRowData->setAccessible(true);
        $row = $formatRowData->invoke(null, 1, $presensi);

        $this->assertEquals(1, $row[0]);
        $this->assertEquals('Ahmad Fulan', $row[1]);
        $this->assertEquals('L', $row[2]);
        $this->assertEquals('Kelompok Alpha', $row[3]);
        $this->assertEquals('Desa Bojongsoang', $row[4]);
        $this->assertEquals('Daerah Bandung Selatan', $row[5]);
        $this->assertEquals('Mahasiswa', $row[6]);
        $this->assertEquals('21', $row[7]);
        $this->assertEquals('Hadir', $row[8]);
        $this->assertEquals('In Time', $row[9]);
        $this->assertStringContainsString('18/10/2026', $row[10]);
    }
}
