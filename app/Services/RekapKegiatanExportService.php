<?php

namespace App\Services;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\LaporanKegiatan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\CSV\Options as CsvOptions;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Writer\XLSX\Options as XlsxOptions;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RekapKegiatanExportService
{
    /**
     * Export presensi data for a given LaporanKegiatan record.
     *
     * @param LaporanKegiatan $laporan
     * @param string|null $keterangan 'Hadir', 'Alfa', or null for all non-izin
     * @param string $format 'xlsx' or 'csv'
     * @return BinaryFileResponse
     */
    public static function export(LaporanKegiatan $laporan, ?string $keterangan = null, string $format = 'xlsx'): BinaryFileResponse
    {
        $laporan->loadMissing('kegiatan');
        $records = self::getRecords($laporan, $keterangan);

        $format = strtolower($format) === 'csv' ? 'csv' : 'xlsx';
        $kegiatanJudul = Str::slug($laporan->kegiatan?->nm_kegiatan ?? 'Kegiatan', '_');
        $detailTingkatan = Str::slug($laporan->detail_tingkatan ?? 'Rekap', '_');
        $tipe = $keterangan ? ucfirst(strtolower($keterangan)) : 'Hadir_Alfa';
        $timestamp = now()->format('Ymd_His');
        $fileName = "Rekap_{$tipe}_{$kegiatanJudul}_{$detailTingkatan}_{$timestamp}.{$format}";

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . DIRECTORY_SEPARATOR . 'export_' . Str::random(16) . '.' . $format;

        if ($format === 'csv') {
            self::generateCsv($tempPath, $records);
            $mimeType = 'text/csv; charset=UTF-8';
        } else {
            self::generateXlsx($tempPath, $laporan, $records, $keterangan);
            $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }

        return response()->download($tempPath, $fileName, [
            'Content-Type' => $mimeType,
        ])->deleteFileAfterSend(true);
    }

    /**
     * Retrieve presensi records matching the report level and status.
     *
     * @param LaporanKegiatan $laporan
     * @param string|null $keterangan
     * @return Collection
     */
    public static function getRecords(LaporanKegiatan $laporan, ?string $keterangan = null): Collection
    {
        $query = Presensi::query()
            ->select('presensis.*')
            ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
            ->where('presensis.kegiatan_id', '=', $laporan->kegiatan_id);

        if ($keterangan) {
            $query->where('presensis.keterangan', $keterangan);
        } else {
            $query->where('presensis.keterangan', '!=', 'Izin');
        }

        if ($laporan->tingkatan_laporan === 'MM Daerah') {
            $id = Daerah::query()->where('nm_daerah', $laporan->detail_tingkatan)->value('id');
            if ($id) {
                $query->where('mudamudis.daerah_id', $id);
            }
        } elseif ($laporan->tingkatan_laporan === 'MM Desa') {
            $id = Desa::query()->where('nm_desa', $laporan->detail_tingkatan)->value('id');
            if ($id) {
                $query->where('mudamudis.desa_id', $id);
            }
        } elseif ($laporan->tingkatan_laporan === 'MM Kelompok') {
            $id = Kelompok::query()->where('nm_kelompok', $laporan->detail_tingkatan)->value('id');
            if ($id) {
                $query->where('mudamudis.kelompok_id', $id);
            }
        }

        return $query->with([
            'mudamudi.kelompok',
            'mudamudi.desa',
            'mudamudi.daerah',
        ])
        ->orderBy('mudamudis.kelompok_id')
        ->orderBy('mudamudis.nama')
        ->get();
    }

    /**
     * Generate an Excel (.xlsx) file using OpenSpout.
     */
    protected static function generateXlsx(string $filePath, LaporanKegiatan $laporan, Collection $records, ?string $keterangan): void
    {
        $options = new XlsxOptions();
        $writer = new XLSXWriter($options);
        $writer->openToFile($filePath);

        // Header Styling based on status
        if ($keterangan === 'Hadir') {
            $headerBgColor = Color::rgb(22, 101, 52); // Emerald Green
        } elseif ($keterangan === 'Alfa') {
            $headerBgColor = Color::rgb(153, 27, 27); // Dark Crimson Red
        } else {
            $headerBgColor = Color::rgb(30, 41, 59); // Slate Blue
        }

        $titleStyle = (new Style())
            ->setFontBold()
            ->setFontSize(14)
            ->setFontColor(Color::rgb(15, 23, 42));

        $metaStyle = (new Style())
            ->setFontSize(10)
            ->setFontColor(Color::rgb(71, 85, 105));

        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor($headerBgColor);

        // Metadata Header
        $tipeLabel = $keterangan ? strtoupper($keterangan) : 'HADIR & ALFA';
        $nmKegiatan = $laporan->kegiatan?->nm_kegiatan ?? 'Kegiatan';
        $waktuMulai = $laporan->kegiatan?->waktu_mulai 
            ? Carbon::parse($laporan->kegiatan->waktu_mulai)->format('d/m/Y H:i') 
            : '-';
        $tingkatan = "{$laporan->tingkatan_laporan} - {$laporan->detail_tingkatan}";

        $writer->addRow(Row::fromValues(["REKAPITULASI PRESENSI KEGIATAN - {$tipeLabel}"], $titleStyle));
        $writer->addRow(Row::fromValues(["Nama Kegiatan : {$nmKegiatan}"], $metaStyle));
        $writer->addRow(Row::fromValues(["Waktu Mulai   : {$waktuMulai} | Tingkatan: {$tingkatan}"], $metaStyle));
        $writer->addRow(Row::fromValues(["Total Data    : " . count($records) . " peserta | Tanggal Export: " . now()->format('d/m/Y H:i:s')], $metaStyle));
        $writer->addRow(Row::fromValues([''])); // Blank spacing row

        // Table Column Headers
        $headers = [
            'No',
            'Nama Lengkap',
            'L/P',
            'Kelompok',
            'Desa',
            'Daerah',
            'Status',
            'Usia',
            'Keterangan',
            'Kedatangan',
            'Waktu Presensi',
        ];
        $writer->addRow(Row::fromValues($headers, $headerStyle));

        // Row Styles (Zebra Striping)
        $styleWhite = (new Style())->setBackgroundColor(Color::WHITE);
        $styleLight = (new Style())->setBackgroundColor(Color::rgb(248, 250, 252));

        foreach ($records as $index => $row) {
            $rowStyle = ($index % 2 === 0) ? $styleWhite : $styleLight;
            $writer->addRow(Row::fromValues(self::formatRowData($index + 1, $row), $rowStyle));
        }

        $writer->close();
    }

    /**
     * Generate a CSV (.csv) file with UTF-8 BOM using OpenSpout.
     */
    protected static function generateCsv(string $filePath, Collection $records): void
    {
        $options = new CsvOptions();
        $options->SHOULD_ADD_BOM = true; // Enables proper UTF-8 decoding in Microsoft Excel
        $writer = new CSVWriter($options);
        $writer->openToFile($filePath);

        $headers = [
            'No',
            'Nama Lengkap',
            'L/P',
            'Kelompok',
            'Desa',
            'Daerah',
            'Status',
            'Usia',
            'Keterangan',
            'Kedatangan',
            'Waktu Presensi',
        ];
        $writer->addRow(Row::fromValues($headers));

        foreach ($records as $index => $row) {
            $writer->addRow(Row::fromValues(self::formatRowData($index + 1, $row)));
        }

        $writer->close();
    }

    /**
     * Helper to format a single record's row values.
     */
    protected static function formatRowData(int $no, Presensi $row): array
    {
        $nama = $row->mudamudi?->nama ?? '-';
        $jk = $row->mudamudi?->jk ?? '-';
        $kelompok = $row->mudamudi?->kelompok?->nm_kelompok ?? '-';
        $desa = $row->mudamudi?->desa?->nm_desa ?? '-';
        $daerah = $row->mudamudi?->daerah?->nm_daerah ?? '-';
        $status = $row->mudamudi?->status ?? '-';
        $usia = $row->mudamudi?->usia ? (string)$row->mudamudi->usia : '-';
        $keterangan = $row->keterangan ?? '-';
        $kedatangan = $row->kedatangan ?? '-';
        $rawUpdatedAt = $row->getAttributes()['updated_at'] ?? null;
        $waktuPresensi = ($row->keterangan === 'Hadir' && $rawUpdatedAt)
            ? Carbon::parse($rawUpdatedAt)->format('d/m/Y H:i:s')
            : '-';

        return [
            $no,
            $nama,
            $jk,
            $kelompok,
            $desa,
            $daerah,
            $status,
            $usia,
            $keterangan,
            $kedatangan,
            $waktuPresensi,
        ];
    }
}
