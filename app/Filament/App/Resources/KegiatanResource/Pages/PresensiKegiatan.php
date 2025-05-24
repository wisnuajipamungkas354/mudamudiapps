<?php

namespace App\Filament\App\Resources\KegiatanResource\Pages;

use App\Filament\App\Resources\KegiatanResource;
use App\Filament\App\Resources\KegiatanResource\Widgets\PresensiWidget;
use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kegiatan;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use App\Models\Presensi;
use App\Models\Registrasi;
use App\Models\LaporanKegiatan;
use App\Models\Status;
use Carbon\Carbon;
use Filament\Actions\Action as ActionPage;
use Filament\Actions\Concerns\HasAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\Actions;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Filament\Support\Enums\ActionSize;

class PresensiKegiatan extends Page implements HasTable
{
    use InteractsWithRecord, InteractsWithTable, InteractsWithInfolists, HasTabs;

    protected static string $resource = KegiatanResource::class;

    protected static string $view = 'filament.app.resources.kegiatan-resource.pages.presensi-kegiatan';

    protected ?string $heading = 'Presensi';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        if ($this->record->detail_tingkatan != auth()->user()->detail) {
            abort(401);
        }

        if($this->record->is_finish) {
            abort(403, 'Mohon Maaf Kegiatan Sudah Selesai');
        }

        if(strtotime($this->record->waktu_selesai) <= strtotime(now())){
            $this->savePresensi();
        }
    }

    public function getUserRole()
    {
        // Mengambil Nama Role User
        $role = Auth::user()->roles[0]->name;
        $result = [
            'nm_role' => $role,
            'nm_tingkatan' => auth()->user()->detail,
        ];
        
        // Transalasi String Field Detail yang ada di User menjadi Id 
        if ($role == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', $result['nm_tingkatan'])->value('id');
            $result['daerah_id'] = $daerah;
        } elseif ($role == 'MM Desa') {
            $daerahDesa = Desa::query()->where('nm_desa', '=', $result['nm_tingkatan'])->first(['daerah_id', 'id as desa_id']);
            $result['daerah_id'] = $daerahDesa->daerah_id ;
            $result['desa_id'] = $daerahDesa->desa_id ;
        } elseif ($role == 'MM Kelompok') {
            $daerahDesaKelompok = Desa::query()->join('kelompoks', 'desas.id', '=', 'kelompoks.desa_id')->join('daerahs', 'desas.daerah_id', '=', 'daerahs.id')->where('kelompoks.nm_kelompok', '=', $result['nm_tingkatan'])->first(['desas.daerah_id as daerah_id', 'desas.id as desa_id', 'kelompoks.id as kelompok_id']);
            $result['daerah_id'] = $daerahDesaKelompok->daerah_id;
            $result['desa_id'] = $daerahDesaKelompok->desa_id;
            $result['kelompok_id'] = $daerahDesaKelompok->kelompok_id;
        }

        // nm_role, detail_tingkatan, daerah_id, desa_id, kelompok_id 
        return $result;
    }

    public function createLaporanKegiatan($items, $rekapData, $kolom, $tingkatan_laporan, $nmTingkatan) {
        foreach($items as $item) {
            $hadirL = $rekapData->where($kolom, $item[$kolom])->where('jk', 'L')->where('keterangan', 'Hadir')->count();
            $hadirP = $rekapData->where($kolom, $item[$kolom])->where('jk', 'P')->where('keterangan', 'Hadir')->count();
            $izinL = $rekapData->where($kolom, $item[$kolom])->where('jk', 'L')->where('keterangan', 'Izin')->count();
            $izinP = $rekapData->where($kolom, $item[$kolom])->where('jk', 'P')->where('keterangan', 'Izin')->count();
            $alfaL = $rekapData->where($kolom, $item[$kolom])->where('jk', 'L')->where('keterangan', 'Alfa')->count();
            $alfaP = $rekapData->where($kolom, $item[$kolom])->where('jk', 'P')->where('keterangan', 'Alfa')->count();
            $inTime = $rekapData->where($kolom, $item[$kolom])->where('kedatangan', 'In Time')->count();
            $onTime = $rekapData->where($kolom, $item[$kolom])->where('kedatangan', 'On Time')->count();
            $overTime = $rekapData->where($kolom, $item[$kolom])->where('kedatangan', 'Overtime')->count();
            $tidakDatang = $rekapData->where($kolom, $item[$kolom])->where('kedatangan', 'Tidak Datang')->count();
            $sakit = $rekapData->where($kolom, $item[$kolom])->where('kategori_izin', 'Sakit')->count();
            $kerja = $rekapData->where($kolom, $item[$kolom])->where('kategori_izin', 'Kerja')->count();
            $kuliah = $rekapData->where($kolom, $item[$kolom])->where('kategori_izin', 'Kuliah')->count();
            $sekolah = $rekapData->where($kolom, $item[$kolom])->where('kategori_izin', 'Sekolah')->count();
            $acaraKeluarga = $rekapData->where($kolom, $item[$kolom])->where('kategori_izin', 'Acara Keluarga')->count();
            $acaraMendesak = $rekapData->where($kolom, $item[$kolom])->where('kategori_izin', 'Acara Mendesak')->count();

            $totalPeserta = $hadirL + $hadirP + $izinL + $izinP + $alfaL + $alfaP;

            $resultRekap = [
                'kegiatan_id' => $this->record->id,
                'tingkatan_laporan' => $tingkatan_laporan,
                'detail_tingkatan' => $item[$nmTingkatan],
                'hadir_l' => $hadirL,
                'hadir_p' => $hadirP,
                'izin_l' => $izinL,
                'izin_p' => $izinP,
                'alfa_l' => $alfaL,
                'alfa_p' => $alfaP,
                'total_peserta' => $totalPeserta,
                'in_time' => $inTime,
                'on_time' => $onTime,
                'over_time' => $overTime,
                'tidak_datang' => $tidakDatang,
                'sakit' => $sakit,
                'kerja' => $kerja,
                'kuliah' => $kuliah,
                'sekolah' => $sekolah,
                'acara_keluarga' => $acaraKeluarga,
                'acara_mendesak' => $acaraMendesak
            ];

            if(!LaporanKegiatan::where('kegiatan_id', '=', $this->record->id)->where('tingkatan_laporan', '=', $tingkatan_laporan)->where('detail_tingkatan', '=', $item[$nmTingkatan])->exists()){
                LaporanKegiatan::create($resultRekap);
            } else {
                LaporanKegiatan::where('kegiatan_id', '=', $this->record->id)->where('tingkatan_laporan', '=', $tingkatan_laporan)->where('detail_tingkatan', '=', $item[$nmTingkatan])->update($resultRekap);
            }
        }
    }

    public function savePresensi() {

        // Get All Peserta
        $role = $this->getUserRole();
        $kegiatan = $this->record;
        $getAllPeserta = [];

        if ($role['nm_role'] == 'MM Daerah') {
            if ($kegiatan->kategori_peserta[0] == 'all') {
                $getAllPeserta = Mudamudi::query()->where('daerah_id', '=', $role['daerah_id']);
            } elseif ($kegiatan->kategori_peserta[0] == 'category') {
                if($kegiatan->kategori_peserta[1] == 'Lepas Pelajar') {
                    $newData = array_slice($kegiatan->kategori_peserta, 2);                
                } else {
                    $newData = array_slice($kegiatan->kategori_peserta, 1);                
                }
                $getAllPeserta = Mudamudi::query()->where('daerah_id', $role['daerah_id'])->whereIn('status', $newData);
            } elseif($kegiatan->kategori_peserta[0] == 'age') {
                $getAllPeserta = Mudamudi::query()->where('daerah_id', $role['daerah_id'])->whereBetween('usia', [$kegiatan->kategori_peserta[1], $kegiatan->kategori_peserta[2]]);
            } 
        } elseif ($role['nm_role'] == 'MM Desa') {
            if ($kegiatan->kategori_peserta[0] == 'all') {
                $getAllPeserta = Mudamudi::query()->where('daerah_id', '=', $role['daerah_id'])->where('desa_id', '=', $role['desa_id']);
            } elseif ($kegiatan->kategori_peserta[0] == 'category') {
                if($kegiatan->kategori_peserta[1] == 'Lepas Pelajar') {                
                    $newData = array_slice($kegiatan->kategori_peserta, 2);                
                } else {
                    $newData = array_slice($kegiatan->kategori_peserta, 1);                
                }
                $getAllPeserta = Mudamudi::query()->where('daerah_id', $role['daerah_id'])->where('desa_id', '=', $role['desa_id'])->whereIn('status', $newData);
            } elseif ($kegiatan->kategori_peserta[0] == 'age') {
                $getAllPeserta = Mudamudi::query()->where('daerah_id', $role['daerah_id'])->where('desa_id', '=', $role['desa_id'])->whereBetween('usia', [$kegiatan->kategori_peserta[1], $kegiatan->kategori_peserta[2]]);
            }
        } elseif ($role['nm_role'] == 'MM Kelompok') {
            if ($kegiatan->kategori_peserta[0] == 'all') {
                $getAllPeserta = Mudamudi::query()->where('daerah_id', '=', $role['daerah_id'])->where('desa_id', '=', $role['desa_id'])->where('kelompok_id', '=', $role['kelompok_id']);
            } elseif ($kegiatan->kategori_peserta[0] == 'category') {
                if($kegiatan->kategori_peserta[1] == 'Lepas Pelajar') {                
                    $newData = array_slice($kegiatan->kategori_peserta, 2);                
                } else {
                    $newData = array_slice($kegiatan->kategori_peserta, 1);                
                }
                $getAllPeserta = Mudamudi::query()->where('daerah_id', $role['daerah_id'])->where('desa_id', '=', $role['desa_id'])->where('kelompok_id', '=', $role['kelompok_id'])->whereIn('status', $newData);
            } elseif ($kegiatan->kategori_peserta[0] == 'age') {
                $newData = array_slice($kegiatan->kategori_peserta, 1);
                $getAllPeserta = Mudamudi::query()->where('daerah_id', $role['daerah_id'])->where('desa_id', '=', $role['desa_id'])->where('kelompok_id', '=', $role['kelompok_id'])->whereBetween('usia', [$kegiatan->kategori_peserta[1], $kegiatan->kategori_peserta[2]]);
            }
        }

        // Create Data MM yang tidak hadir ke tabel presensi kegiatan
        foreach ($getAllPeserta->get('id') as $id) {
            if (!DB::table('presensis')->where('kegiatan_id', $kegiatan->id)->where('mudamudi_id', $id->id)->exists()) {
                Presensi::create([
                    'kegiatan_id' => $kegiatan->id,
                    'mudamudi_id' => $id->id,
                ]);
            }
        }

        // Rekap Data Presensi Per Kelompok
        $rekapDataPresensi = DB::table('presensis')
        ->join('kegiatans', 'presensis.kegiatan_id', '=', 'kegiatans.id')
        ->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')
        ->where('presensis.kegiatan_id', $this->record->id)
        ->get([
            'kegiatans.tingkatan_kegiatan', 
            'kegiatans.detail_tingkatan',
            'presensis.kegiatan_id',
            'mudamudis.daerah_id', 
            'mudamudis.desa_id', 
            'mudamudis.kelompok_id', 
            'mudamudis.jk', 
            'presensis.keterangan', 
            'presensis.kedatangan', 
            'presensis.kategori_izin', 
            'presensis.ket_izin'
        ]);


        if($role['nm_role'] == 'MM Daerah') {
            $dataDesaKelompok = Desa::query()->join('kelompoks', 'desas.id', '=', 'kelompoks.desa_id')->join('daerahs', 'desas.daerah_id', '=', 'daerahs.id')->where('desas.daerah_id', $role['daerah_id'])->get(['desas.daerah_id', 'daerahs.nm_daerah', 'desas.id as desa_id', 'desas.nm_desa', 'kelompoks.id as kelompok_id', 'kelompoks.nm_kelompok']);

            $kelompok = $dataDesaKelompok->where('daerah_id', $role['daerah_id'])->toArray();
            $desa = $dataDesaKelompok->where('daerah_id', $role['daerah_id'])->unique('desa_id')->toArray();
            $daerah = $dataDesaKelompok->where('daerah_id', $role['daerah_id'])->unique('daerah_id')->toArray();
            
            // Create Laporan Ke Kelompok-kelompok
            $this->createLaporanKegiatan($kelompok, $rekapDataPresensi, 'kelompok_id', 'MM Kelompok', 'nm_kelompok');

            // Create Laporan ke Desa-desa
            $this->createLaporanKegiatan($desa, $rekapDataPresensi, 'desa_id', 'MM Desa', 'nm_desa');

            // Create Laporan Daerah
            $this->createLaporanKegiatan($daerah, $rekapDataPresensi, 'daerah_id', 'MM Daerah', 'nm_daerah');

        } elseif($role['nm_role'] == 'MM Desa') {
            $dataDesaKelompok = Desa::query()->join('kelompoks', 'desas.id', '=', 'kelompoks.desa_id')->where('desas.id', $role['desa_id'])->get(['desas.id as desa_id', 'desas.nm_desa', 'kelompoks.id as kelompok_id', 'kelompoks.nm_kelompok']);

            $kelompok = $dataDesaKelompok->where('desa_id', $role['desa_id'])->toArray();
            $desa = $dataDesaKelompok->where('desa_id', $role['desa_id'])->unique('desa_id')->toArray();

            // Create Laporan Ke Kelompok-kelompok
            $this->createLaporanKegiatan($kelompok, $rekapDataPresensi, 'kelompok_id', 'MM Kelompok', 'nm_kelompok');

            // Create Laporan ke Desa-desa
            $this->createLaporanKegiatan($desa, $rekapDataPresensi, 'desa_id', 'MM Desa', 'nm_desa');

        } else {
            $dataKelompok = Kelompok::query()->where('id', $role['kelompok_id'])->get(['id as kelompok_id', 'nm_kelompok'])->toArray();            

            // Create Laporan Ke Kelompok-kelompok
            $this->createLaporanKegiatan($dataKelompok, $rekapDataPresensi, 'kelompok_id', 'MM Kelompok', 'nm_kelompok');
        }
        

        DB::table('kegiatans')->where('id', $this->record->id)->update(['is_finish' => true]);
        redirect('/kegiatans');
    }

    public function getHeaderActions(): array
    {
        return [
            ActionPage::make('form_presensi')
                ->label('Link Presensi')
                ->color('primary')
                ->url('/presensi-mudamudi/' . $this->record->id)
                ->outlined()
                ->extraAttributes([
                    'target' => '_blank',
                ])
                ->size(ActionSize::Small),
            ActionPage::make('save')
                ->label('Tutup Presensi')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tutup Presensi')
                ->modalDescription('Apakah Presensi sudah benar-benar selesai? Seluruh data akan disimpan & tidak dapat diubah lagi!')
                ->size(ActionSize::Small)
                ->action(function() {
                    $this->savePresensi();
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {

        return $infolist
            ->record($this->record)
            ->schema([
                Tabs::make('Informasi Kegiatan')
                    ->tabs([
                        Tabs\Tab::make('Live Count')
                            ->schema([
                                TextEntry::make('total_hadir')
                                    ->label('Hadir')
                                    ->icon('heroicon-o-check-circle')
                                    ->iconColor('success')
                                    ->getStateUsing(function () {
                                        if ($this->record->kategori_peserta[0] !== 'Keputrian') {;
                                            $laki = DB::table('presensis')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $this->record->id)->where('presensis.keterangan', 'Hadir')->where('mudamudis.jk', 'L')->count();
                                            $perempuan = DB::table('presensis')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $this->record->id)->where('presensis.keterangan', 'Hadir')->where('mudamudis.jk', 'P')->count();
                                            $total = DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('keterangan', 'Hadir')->count();
                                            return <<<TEXT
                                            <div class="flex flex-row gap-3">
                                                <span>Laki-laki: $laki</span>
                                                <span>Perempuan: $perempuan</span>
                                                <span>Total: $total</span>
                                            </div>
                                            TEXT;
                                        } else {
                                            $query = DB::table('presensis')
                                                ->where('kegiatan_id', $this->record->id)
                                                ->where('keterangan', 'Hadir');
                                            $total = $query->count();
                                            return $total;
                                        }
                                    })
                                    ->html(true)
                                    ->weight(FontWeight::SemiBold)
                                    ->columnSpan(2),
                                TextEntry::make('total_izin')
                                    ->label('Izin')
                                    ->icon('heroicon-o-information-circle')
                                    ->iconColor('warning')
                                    ->getStateUsing(function () {
                                        if ($this->record->kategori_peserta !== 'Keputrian') {;
                                            $laki = DB::table('presensis')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $this->record->id)->where('presensis.keterangan', 'Izin')->where('mudamudis.jk', 'L')->count();
                                            $perempuan = DB::table('presensis')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $this->record->id)->where('presensis.keterangan', 'Izin')->where('mudamudis.jk', 'P')->count();
                                            $total = DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('keterangan', 'Izin')->count();
                                            return <<<TEXT
                                            <div class="flex flex-row gap-3">
                                                <span>Laki-laki: $laki</span>
                                                <span>Perempuan: $perempuan</span>
                                                <span>Total: $total</span>
                                            </div>
                                            TEXT;
                                        } else {
                                            $hadir = DB::table('presensis')
                                                ->where('kegiatan_id', $this->record->id)
                                                ->where('hadir')->value('hadir');
                                            // $hadir = JSON($hadir);
                                            $total = $hadir;
                                            return $total;
                                        }
                                    })
                                    ->html(true)
                                    ->weight(FontWeight::SemiBold)
                                    ->columnSpan(2),
                            ])
                            ->columns(
                                [
                                    'sm' => 2,
                                    'md' => 4,
                                    'xl' => 4,
                                ]
                            ),
                        Tabs\Tab::make('Informasi Kegiatan')
                            ->schema([
                                TextEntry::make('nm_kegiatan')
                                    ->label('Judul Kegiatan'),
                                TextEntry::make('tempat_kegiatan')
                                    ->label('Tempat Kegiatan'),
                                TextEntry::make('waktu_mulai')
                                    ->label('Waktu Pelaksanaan'),
                                TextEntry::make('kategori_peserta')
                                    ->label('Kategori Peserta')
                                    // ->,
                            ])
                            ->columns(4)
                    ])
            ]);
    }

    // Tabel List Peserta
    public function table(Table $table): Table
    {
        if($this->record->is_finish) {
            abort(403, 'Mohon Maaf Kegiatan Sudah Selesai');
        }

        if(strtotime($this->record->waktu_selesai) <= strtotime(now())){
            $this->savePresensi();

            Notification::make()
            ->title('Presensi telah Berhasil Disimpan!')
            ->color('success')
            ->success()
            ->body('Rekap Presensi sudah disimpan!')
            ->seconds(5)
            ->send();
        }

        $peserta = Presensi::query()->where('kegiatan_id', $this->record->id)->whereNot('keterangan', 'Izin')->orderBy('updated_at', 'desc');
        // $peserta = Mudamudi::query();
        
        return $table
            ->headerActions([
                Action::make('refresh')
                    ->label('Refresh')
            ])
            ->query($peserta)
            ->columns([
                // TextColumn::make('presensis.no_peserta')
                //     ->label('No Peserta')
                //     ->getStateUsing(fn (Mudamudi $record) => DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->mudamudi_id)->value('no_peserta')),
                // TextColumn::make('presensis.updated_at')
                //     ->label('Jam')
                //     ->getStateUsing(fn (Mudamudi $record) => DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->mudamudi_id)->value('updated_at'))
                //     ->dateTime('H:i'),
                TextColumn::make('mudamudi.kelompok.nm_kelompok')
                    ->searchable(),
                TextColumn::make('mudamudi.nama')
                    ->searchable(),
                TextColumn::make('mudamudi.jk')
                    ->label('L/P'),
                TextColumn::make('mudamudi.status')
                    ->label('Status'),
                TextColumn::make('updated_at')
                    ->label('Jam')
                    ->dateTime('H:i'),
                TextColumn::make('kedatangan')
                    ->label('Kedatangan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'In Time' => 'primary',
                        'On Time' => 'success',
                        'Overtime' => 'warning',
                        'Tidak Datang' => 'danger',
                    })
                    ->getStateUsing(fn (Presensi $record) => DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->mudamudi_id)->value('kedatangan'))
                    
            ])
            ->filters([
                SelectFilter::make('Desa')
                    ->relationship('mudamudi.desa', 'nm_desa')
                    ->visible(fn () => auth()->user()->roles[0]->name === 'MM Daerah'),
                // SelectFilter::make('mudamudi.kelompok_id')
                //     ->label('Kelompok')
                //     ->options(function() {
                //         if(auth()->user()->roles[0]->name === 'MM Daerah') {
                //             return Kelompok::query()->pluck('nm_kelompok', 'id')->toArray();
                //         } else {
                //             $desaId = Desa::query()->where('nm_desa', auth()->user()->detail)->value('id');
                //             return Kelompok::query()->where('desa_id', $desaId)->pluck('nm_kelompok', 'id')->toArray();
                //         }
                //     })
                //     ->attribute('mudamudi.kelompok_id'),
                // SelectFilter::make('mudamudi.status')
                //     ->label('Status')
                //     ->options(fn () => Status::query()->pluck('nm_status', 'nm_status'))
                //     ->multiple()
                //     ->default(function() {
                //         if($this->record->kategori_peserta[0] == 'category') {
                //             $newKategori = array_slice($this->record->kategori_peserta, 1);
                //             return $newKategori;
                //         } else {
                //             return null;
                //         }
                //     }),
                SelectFilter::make('kedatangan')
                    ->label('Kedatangan')
                    ->options(['On Time' => 'On Time', 'In Time' => 'In Time', 'Overtime' => 'Overtime']),
            ])
            ->actions([
                // Tables\Actions\Action::make('hadir')
                //     ->label('Hadir')
                //     ->color('success')
                //     ->icon('heroicon-o-check-circle')
                //     ->action(function (Mudamudi $record) {
                //         $kegiatan = $this->record;
                //         $now = Carbon::now();
                //         $waktuKegiatan = '';
                //         $onTime = '';
                //         $kedatangan = '';

                //         // Penentu Waktu Mulai Kegiatan
                //         $waktuKegiatan = Carbon::parse($kegiatan->waktu_mulai);
                //         $onTime = Carbon::parse($this->record->waktu_mulai)->addMinutes(15);

                //         // Penentuan Kategori Kedatangan
                //         if ($now < $waktuKegiatan) {
                //             $kedatangan = 'In Time';
                //         } elseif ($waktuKegiatan <= $now && $now <= $onTime) {
                //             $kedatangan = 'On Time';
                //         } elseif ($onTime < $now) {
                //             $kedatangan = 'Overtime';
                //         }

                //         if (DB::table('presensis')->where('kegiatan_id', $kegiatan->id)->where('mudamudi_id', $record->id)->exists()) {
                //             DB::table('presensis')->where('kegiatan_id', $kegiatan->id)->where('mudamudi_id', $record->id)->update([
                //                 'keterangan' => 'Hadir',
                //                 'kedatangan' => $kedatangan,
                //                 'kategori_izin' => null,
                //                 'ket_izin' => null,
                //                 'updated_at' => $now
                //             ]);
                //         } else {
                //             Presensi::create([
                //                 'kegiatan_id' => $kegiatan->id,
                //                 'mudamudi_id' => $record->id,
                //                 'keterangan' => 'Hadir',
                //                 'kedatangan' => $kedatangan,
                //             ]);
                //         }
                //     }),
                // Tables\Actions\Action::make('izin')
                //     ->label('Izin')
                //     ->color('warning')
                //     ->icon('heroicon-o-information-circle')
                //     ->action(function (Mudamudi $record) {
                //         if (DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id)->exists()) {
                //             DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id)->update([
                //                 'keterangan' => 'Izin',
                //                 'kedatangan' => 'Tidak Datang',
                //             ]);
                //         } else {
                //             Presensi::create([
                //                 'kegiatan_id' => $this->record->id,
                //                 'mudamudi_id' => $record->id,
                //                 'keterangan' => 'Izin',
                //                 'kedatangan' => 'Tidak Datang'
                //             ]);
                //         }
                //     }),
                // Tables\Actions\Action::make('alfa')
                //     ->label('Alfa')
                //     ->color('danger')
                //     ->icon('heroicon-o-x-circle')
                //     ->action(function (Presensi $record) {
                //         if (DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id)->exists()) {
                //             DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id)->update([
                //                 'keterangan' => 'Alfa',
                //                 'kedatangan' => 'Tidak Datang',
                //             ]);
                //         } else {
                //             Presensi::create([
                //                 'kegiatan_id' => $this->record->id,
                //                 'mudamudi_id' => $record->id,
                //                 'keterangan' => 'Alfa',
                //                 'kedatangan' => 'Tidak Datang'
                //             ]);
                //         }
                //     }),
            ])
            ->bulkActions([
                // 
            ])
            ->striped()
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum ada yang hadir')
            ->poll('10s');
    }

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()->where('tingkatan_kegiatan', auth()->user()->roles[0]->name)->where('detail_tingkatan', auth()->user()->detail);
    }
}
