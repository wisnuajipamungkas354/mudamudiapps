<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\KegiatanResource\Pages;
use App\Filament\App\Resources\KegiatanResource\RelationManagers;
use App\Models\Kegiatan;
use App\Models\Mudamudi;
use App\Models\SesiKegiatan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Presensi';

    protected static ?string $navigationLabel = 'Kegiatan';
    
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
       return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('nm_kegiatan')
                            ->label('Judul Kegiatan')
                            ->placeholder('Masukkan Judul Kegiatan')
                            ->required(),
                        Forms\Components\TextInput::make('tempat_kegiatan')
                            ->label('Tempat Kegiatan')
                            ->placeholder('Masukkan Tempat Kegiatan')
                            ->required(),
                        Forms\Components\DateTimePicker::make('waktu_mulai')
                            ->label('Waktu Mulai')
                            ->placeholder('Masukkan Waktu Mulai')
                            ->displayFormat('d/m/Y')
                            ->minDate(date('Y-m-d H:i'))
                            ->seconds(false)
                            ->live()
                            ->required(),
                        Forms\Components\DateTimePicker::make('waktu_selesai')
                            ->label('Waktu Selesai')
                            ->placeholder('Masukkan Waktu Selesai')
                            ->displayFormat('H:i d/m/Y')
                            ->minDate(fn(Get $get) => $get('waktu_mulai'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\Select::make('kategori_peserta')
                            ->label('Kategori Peserta')
                            ->options([
                                'Semua Muda-Mudi' => 'Semua Muda-mudi',
                                'Pelajar SMP' => 'Pelajar SMP',
                                'Pelajar SMA/K' => 'Pelajar SMA/K',
                                'Pelajar SMP & SMA/K' => 'Pelajar SMP & SMA/K',
                                'Mahasiswa' => 'Mahasiswa',
                                'Lepas Pelajar' => 'Lepas Pelajar',
                                'Keputrian' => 'Keputrian',
                            ])
                            ->live()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('kode_kegiatan')
                            ->label('Kode Kegiatan')
                            ->placeholder('Masukkan Kode (6 digit)')
                            ->autocomplete(false)
                            ->required()
                            ->minLength(6)
                            ->maxLength(6)
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('waktu_mulai')
                    ->label('Waktu')
                    ->dateTime('H:i d/m/Y'),
                Tables\Columns\TextColumn::make('nm_kegiatan')
                    ->label('Judul Kegiatan'),
                Tables\Columns\TextColumn::make('tempat_kegiatan')
                    ->label('Tempat'),
                Tables\Columns\TextColumn::make('tingkatan_kegiatan')
                    ->label('Tingkatan Kegiatan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('detail_tingkatan')
                    ->label('Detail Tingkatan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kategori_peserta')
                    ->label('Kategori Peserta'),
                Tables\Columns\TextColumn::make('kode_kegiatan')
                    ->label('Kode Kegiatan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('share_link')
                    ->label('Umumin')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->url(function(Kegiatan $record): string {
                        setlocale(LC_ALL, 'id-ID', 'id_ID');
                        $waktu = strftime("%A, %d %B %Y", strtotime($record->waktu_mulai));
                        $jam = date('H:i', strtotime($record->waktu_mulai));
                        $linkPresensi = url('presensi-mudamudi/' . $record->id);
                        $url = "https://api.whatsapp.com/send?text=🙏🏻 السلام عليكم ورحمة الله وبركاته 🙏🏻
%0A
%0A📢 Menginformasikan
%0ASehubungan dengan akan dilaksanakannya :
%0A
%0A
💾 *{$record->nm_kegiatan}*
%0A
%0A📆 {$waktu}
%0A⏰ {$jam} s/d Selesai
%0A📍 {$record->tempat_kegiatan}
%0A👳🏻‍♀🧕 _{$record->kategori_peserta}_
%0A
%0A*NB:*
%0A- Membawa sodaqoh lemparan
%0A- Amal Sholih supaya diusahakan hadir tepat waktu
%0A
%0A*Link Presensi*
%0A_Adapun link presensi kegiatan dapat diakses melalui link dibawah ini :_
%0A{$linkPresensi}
%0A
%0A_Jika *berhalangan hadir*, mohon amal sholih bisa mengisi *form perizinan* pada link presensi diatas._
%0A
%0ADitetapi dan Dikerjakan karna Allah, semoga Allah paring kesemangatan, aman, selamat, lancar, sukses dan barokah
%0A
%0A
الحمدلله جزاكم الله خيرا 😊🙏🏻";
                        
                        return $url;
                    })
                    ->color('success')
                    ->visible(fn(Kegiatan $record) => $record->is_finish ? false : true),
                Tables\Actions\Action::make('qr_download')
                    ->label('QR-Code')
                    ->url(function(Kegiatan $record): string {
                        $path = 'public/qr-images/kegiatan/'.$record->id . '.png';
                        $url = Storage::url($path);
                        return $url;
                    })
                    ->extraAttributes(fn(Kegiatan $record) => ['download' => $record->nm_kegiatan])
                    ->icon('heroicon-s-qr-code')
                    ->color('warning')
                    ->visible(fn(Kegiatan $record) => $record->is_finish ? false : true),
                Tables\Actions\Action::make('presensi')
                    ->label('Presensi')
                    ->url(fn(Kegiatan $record) =>  'kegiatans/'  . $record->id . '/presensi')
                    ->icon('heroicon-o-book-open')
                    ->visible(function(Kegiatan $record) {
                        if(Carbon::parse($record->waktu_mulai)->addMinutes(-30) > Carbon::now()){
                            return false;
                        }

                        if($record->is_finish) {
                            return false;
                        } else {
                            return true;
                        }
                    }),
                // Tables\Actions\Action::make('lihat_rekap')
                //     ->label('Lihat Rekapitulasi')
                //     ->color('success')
                //     ->icon('heroicon-o-arrow-trending-up')
                //     ->url('/rekapitulasi-kegiatan')
                //     ->visible(fn(Kegiatan $record) => $record->is_finish ? true : false),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Kegiatan')
                    ->modalDescription('Apakah kamu yakin data kegiatan ini akan dihapus?')
                    ->modalSubmitActionLabel('Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Item Terpilih')
                        ->modalHeading('Hapus Kegiatan')
                        ->modalDescription('Apakah kamu yakin data kegiatan ini akan dihapus?')
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Batal'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kegiatan');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKegiatans::route('/'),
            'create' => Pages\CreateKegiatan::route('/create'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
            'presensi' => Pages\PresensiKegiatan::route('/{record}/presensi')
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tingkatan_kegiatan', auth()->user()->roles[0]->name)->where('detail_tingkatan', auth()->user()->detail);
    }
}
