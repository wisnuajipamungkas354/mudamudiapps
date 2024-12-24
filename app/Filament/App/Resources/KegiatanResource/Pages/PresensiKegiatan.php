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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if($this->record->is_finish == true) {
            abort(403, 'MAAF, PRESENSI SUDAH DITUTUP!');
        }
    }

    public function getUserRole()
    {
        // Mengambil Nama Role User
        $role = Auth::user()->roles;
        $daerah = '';
        $desa = '';
        $kelompok = '';
        // Transalasi String Field Detail yang ada di User menjadi Id 
        if ($role[0]->name == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first(['id']);
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']);
        }

        // nama role, id daerah, desa (id daerah, id desa), kelompok (id daerah, id desa, id kelompok) 
        return [$role[0]->name, $daerah, $desa, $kelompok];
    }


    public function savePresensi() {
        $rekapData = DB::table('presensis')->join('kegiatans', 'presensis.kegiatan_id', '=', 'kegiatans.id')->join('mudamudis', 'presensis.mudamudi_id', '=', 'mudamudis.id')->where('presensis.kegiatan_id', $this->record->id)->get(['kegiatans.tingkatan_kegiatan', 'kegiatans.detail_tingkatan','presensis.kegiatan_id' ,'mudamudis.daerah_id', 'mudamudis.desa_id', 'mudamudis.kelompok_id', 'mudamudis.jk', 'presensis.keterangan', 'presensis.kedatangan']);
        $KelompokId = [];
        $userRole = $this->getUserRole();

        if($userRole[0] == 'MM Daerah') {
            $KelompokId = DB::table('kelompoks')->join('desas', 'kelompoks.desa_id', '=', 'desas.id')->where('desas.daerah_id', $userRole[1]->id)->get(['desas.daerah_id', 'desas.id as desa_id', 'kelompoks.id as kelompok_id']);
        } elseif($userRole[0] == 'MM Desa') {
            $KelompokId = DB::table('kelompoks')->join('desas', 'kelompoks.desa_id', '=', 'desas.id')->where('desas.id', $userRole[2]->id)->get(['desas.daerah_id', 'desas.id as desa_id', 'kelompoks.id as kelompok_id']);
        } elseif($userRole[0] == 'MM Kelompok') {
            $KelompokId = DB::table('kelompoks')->join('desas', 'kelompoks.desa_id', '=', 'desas.id')->where('kelompoks.id', $userRole[3]->id)->first(['desas.daerah_id', 'desas.id as desa_id', 'kelompoks.id as kelompok_id']);
        }
        
        foreach($KelompokId as $item) {
            $hadirL = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'L')->where('keterangan', 'Hadir')->count();
            $hadirP = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'P')->where('keterangan', 'Hadir')->count();
            $izinL = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'L')->where('keterangan', 'Izin')->count();
            $izinP = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'P')->where('keterangan', 'Izin')->count();
            $alfaL = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'L')->where('keterangan', 'Alfa')->count();
            $alfaP = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'P')->where('keterangan', 'Alfa')->count();
            $inTime = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'P')->where('kedatangan', 'In Time')->count();
            $onTime = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'P')->where('kedatangan', 'On Time')->count();
            $overTime = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'P')->where('kedatangan', 'Overtime')->count();
            $tidakDatang = $rekapData->where('daerah_id', $item->daerah_id)->where('desa_id', $item->desa_id)->where('kelompok_id', $item->kelompok_id)->where('jk', 'P')->where('kedatangan', 'Tidak Datang')->count();

            $totalPeserta = $hadirL + $hadirP + $izinL + $izinP + $alfaL + $alfaP;

            $resultRekap = [
                'kegiatan_id' => $this->record->id,
                'daerah_id' => $item->daerah_id,
                'desa_id' => $item->desa_id,
                'kelompok_id' => $item->kelompok_id,
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
                'tidak_datang' => $tidakDatang
            ];
            LaporanKegiatan::create($resultRekap);
        }

        DB::table('kegiatans')->where('id', $this->record->id)->update(['is_finish' => true]);
        redirect('/kegiatans');
    }

    
    public function getHeaderActions(): array
    {
        return [
            ActionPage::make('save')
                ->label('Tutup Presensi')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tutup Presensi')
                ->modalDescription('Apakah Presensi sudah benar-benar selesai? Seluruh data akan disimpan & tidak dapat diubah lagi!')
                ->action(function() {
                    $this->savePresensi();
                })
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
                                        if ($this->record->kategori_peserta !== 'Keputrian') {;
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
                                    ->label('Kategori Peserta'),

                            ])
                            ->columns(4)
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        $role = $this->getUserRole();
        $kegiatan = $this->record;
        $peserta = '';
        // dd(Carbon::parse($kegiatan->waktu_mulai) < Carbon::now());

        if ($role[0] == 'MM Daerah') {
            if ($kegiatan->kategori_peserta == 'Semua Muda-Mudi') {
                $peserta = Mudamudi::query()->where('daerah_id', '=', $role[1]->id);
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMP') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[1]->id)->where('status', 'Pelajar SMP');
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMA/K') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[1]->id)->where('status', 'LIKE', 'Pelajar %')->whereNot('status', 'Pelajar SMP');
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMP & SMA/K') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[1]->id)->where('status', 'LIKE', 'Pelajar%');
            } elseif ($kegiatan->kategori_peserta == 'Mahasiswa') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[1]->id)->whereNot('status', 'LIKE', 'P%')->whereNot('status', 'LIKE', 'Tenaga%')->whereNot('status', 'LIKE', 'Karyawan%')->whereNot('status', 'LIKE', 'W%');
            } elseif ($kegiatan->kategori_peserta == 'Lepas Pelajar') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[1]->id)->whereNot('status', 'LIKE', 'Pelajar%');
            } elseif ($kegiatan->kategori_peserta == 'Keputrian') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[1]->id)->where('jk', 'P');
            }
        } elseif ($role[0] == 'MM Desa') {
            if ($kegiatan->kategori_peserta == 'Semua Muda-Mudi') {
                $peserta = Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id);
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMP') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', 'Pelajar SMP');
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMA/K') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', 'LIKE', 'Pelajar %')->whereNot('status', 'Pelajar SMP');
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMP & SMA/K') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('status', 'LIKE', 'Pelajar%');
            } elseif ($kegiatan->kategori_peserta == 'Mahasiswa') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereNot('status', 'LIKE', 'P%')->whereNot('status', 'LIKE', 'Tenaga%')->whereNot('status', 'LIKE', 'Karyawan%')->whereNot('status', 'LIKE', 'W%');
            } elseif ($kegiatan->kategori_peserta == 'Lepas Pelajar') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->whereNot('status', 'LIKE', 'Pelajar%');
            } elseif ($kegiatan->kategori_peserta == 'Keputrian') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('jk', 'P');
            }
        } elseif ($role[0] == 'MM Kelompok') {
            if ($kegiatan->kategori_peserta == 'Semua Muda-Mudi') {
                $peserta = Mudamudi::query()->where('daerah_id', '=', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('kelompok_id', '=', $role[3]->id);
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMP') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('status', 'Pelajar SMP');
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMA/K') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('status', 'LIKE', 'Pelajar %')->whereNot('status', 'Pelajar SMP');
            } elseif ($kegiatan->kategori_peserta == 'Pelajar SMP & SMA/K') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('status', 'LIKE', 'Pelajar%');
            } elseif ($kegiatan->kategori_peserta == 'Mahasiswa') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->whereNot('status', 'LIKE', 'P%')->whereNot('status', 'LIKE', 'Tenaga%')->whereNot('status', 'LIKE', 'Karyawan%')->whereNot('status', 'LIKE', 'W%');
            } elseif ($kegiatan->kategori_peserta == 'Lepas Pelajar') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->whereNot('status', 'LIKE', 'Pelajar%');
            } elseif ($kegiatan->kategori_peserta == 'Keputrian') {
                $peserta = Mudamudi::query()->where('daerah_id', $role[2]->daerah_id)->where('desa_id', '=', $role[2]->id)->where('kelompok_id', '=', $role[3]->id)->where('jk', 'P');
            }
        }

        $create = $peserta;

        // Inisialisasi Peserta kedalam tabel presensi
        foreach ($create->get('id') as $id) {
            if (!DB::table('presensis')->where('kegiatan_id', $kegiatan->id)->where('mudamudi_id', $id->id)->exists()) {
                Presensi::create([
                    'kegiatan_id' => $kegiatan->id,
                    'mudamudi_id' => $id->id,
                ]);
            }
        }

        return $table
            ->query($peserta)
            ->columns([
                TextColumn::make('kelompok.nm_kelompok')
                    ->searchable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('jk')
                    ->label('L/P'),
                TextColumn::make('status'),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Alfa' => 'danger',
                    })
                    ->getStateUsing(function (Mudamudi $record) {
                        $peserta = DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id);
                        if ($peserta->exists()) {
                            return $peserta->value('keterangan');
                        } else {
                            return 'Alfa';
                        }
                    })
            ])
            ->filters([
                SelectFilter::make('Desa')
                    ->relationship('desa', 'nm_desa'),
                SelectFilter::make('Kelompok')
                    ->relationship('kelompok', 'nm_kelompok'),
                SelectFilter::make('siap_nikah')
                    ->label('Siap Nikah')
                    ->options(['Siap' => 'Siap', 'Belum' => 'Belum'])
            ])
            ->actions([
                Tables\Actions\Action::make('hadir')
                    ->label('Hadir')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Mudamudi $record) {
                        $kegiatan = $this->record;
                        $now = Carbon::now();
                        $waktuKegiatan = '';
                        $onTime = '';
                        $kedatangan = '';

                        // Penentu Waktu Mulai Kegiatan
                        $waktuKegiatan = Carbon::parse($kegiatan->waktu_mulai);
                        $onTime = Carbon::parse($this->record->waktu_mulai)->addMinutes(15);

                        // Penentuan Kategori Kedatangan
                        if ($now < $waktuKegiatan) {
                            $kedatangan = 'In Time';
                        } elseif ($waktuKegiatan <= $now && $now <= $onTime) {
                            $kedatangan = 'On Time';
                        } elseif ($onTime < $now) {
                            $kedatangan = 'Overtime';
                        }

                        if (DB::table('presensis')->where('kegiatan_id', $kegiatan->id)->where('mudamudi_id', $record->id)->exists()) {
                            DB::table('presensis')->where('kegiatan_id', $kegiatan->id)->where('mudamudi_id', $record->id)->update([
                                'keterangan' => 'Hadir',
                                'kedatangan' => $kedatangan,
                                'updated_at' => $now
                            ]);
                        } else {
                            Presensi::create([
                                'kegiatan_id' => $kegiatan->id,
                                'mudamudi_id' => $record->id,
                                'keterangan' => 'Hadir',
                                'kedatangan' => $kedatangan,
                            ]);
                        }
                    }),
                Tables\Actions\Action::make('izin')
                    ->label('Izin')
                    ->color('warning')
                    ->icon('heroicon-o-information-circle')
                    ->action(function (Mudamudi $record) {
                        if (DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id)->exists()) {
                            DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id)->update([
                                'keterangan' => 'Izin',
                                'kedatangan' => 'Tidak Datang',
                            ]);
                        } else {
                            Presensi::create([
                                'kegiatan_id' => $this->record->id,
                                'mudamudi_id' => $record->id,
                                'keterangan' => 'Izin',
                                'kedatangan' => 'Tidak Datang'
                            ]);
                        }
                    }),
                Tables\Actions\Action::make('alfa')
                    ->label('Alfa')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->action(function (Mudamudi $record) {
                        if (DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id)->exists()) {
                            DB::table('presensis')->where('kegiatan_id', $this->record->id)->where('mudamudi_id', $record->id)->update([
                                'keterangan' => 'Alfa',
                                'kedatangan' => 'Tidak Datang',
                            ]);
                        } else {
                            Presensi::create([
                                'kegiatan_id' => $this->record->id,
                                'mudamudi_id' => $record->id,
                                'keterangan' => 'Alfa',
                                'kedatangan' => 'Tidak Datang'
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                // 
            ])
            ->striped()
            ->defaultPaginationPageOption(5);
    }

}
