<?php

namespace App\Filament\Exports;

use App\Models\Mudamudi;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;

class MudamudiExporter extends Exporter
{
    protected static ?string $model = Mudamudi::class;

    public function getOptions(): array
    {

        return [
            (new Options)
            ->setColumnWidthForRange(40, 1, 12),
        ];
    }
    
    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(12)
            ->setFontName('Times New Roman')
            ->setFontColor(Color::BLACK)
            ->setBackgroundColor(Color::BLUE)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder(new Border(new BorderPart(Border::BOTTOM, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)));
    }

    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(11)
            ->setFontName('Times New Roman')
            ->setShouldWrapText(false);
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('daerah.nm_daerah')
                ->label('Daerah'),
            ExportColumn::make('desa.nm_desa')
                ->label('Desa'),
            ExportColumn::make('kelompok.nm_kelompok')
                ->label('Kelompok'),
            ExportColumn::make('nama')
                ->label('Nama Lengkap'),
            ExportColumn::make('jk')
                ->label('Jenis Kelamin'),
            ExportColumn::make('kota_lahir')
                ->label('Kota Lahir'),
            ExportColumn::make('tgl_lahir')
                ->label('Tanggal Lahir'),
            ExportColumn::make('mubaligh')
                ->label('Mubaligh'),
            ExportColumn::make('status')
                ->label('Status'),
            ExportColumn::make('detail_status')
                ->label('Detail Status'),
            ExportColumn::make('usia')
                ->label('Usia'),
            ExportColumn::make('siap_nikah')
                ->label('Siap Nikah'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your mudamudi export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

}
