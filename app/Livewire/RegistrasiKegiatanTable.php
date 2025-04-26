<?php
 
namespace App\Livewire;

use App\Models\Daerah;
use App\Models\Desa;
use App\Models\Kelompok;
use App\Models\Mudamudi;
use App\Models\Registrasi;
use App\Models\Riwayat;
use App\Models\Presensi;
use App\Models\Shop\Product;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RegistrasiKegiatanTable extends Component implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    public $kegiatan;

    public function mount($kegiatan) {
        $this->kegiatan = $kegiatan;
    }

    public function getUserRole() {
        $role = Auth::user()->roles[0]->name;
        $result = [
            'role' => $role,
            'name' => auth()->user()->detail,
        ];

        if($role === 'MM Daerah') {
            $result['daerah_id'] = Daerah::query()->where('nm_daerah', auth()->user()->detail)->value('id');
        } elseif($role === 'MM Desa') {
            $result = array_push($result, Desa::query()->where('nm_desa', auth()->user()->detail)->first(['daerah_id', 'id as desa_id']));
        } else {
            $result = array_push($result, Desa::query()->join('kelompoks', 'desas.id', '=', 'kelompoks.desa_id')->join('daerahs', 'desas.daerah_id', '=', 'daerahs.id')->where('kelompoks.nm_kelompok', auth()->user()->detail)->first(['daerahs.id as daerah_id', 'desas.id as desa_id', 'kelompoks.id as kelompok_id']));
        }

        return $result;
    }

    public function table(Table $table): Table
    {
    
        return $table
            ->query(Registrasi::query())
            ->headerActions([
                Action::make('refresh')
                    ->label('Refresh')
            ])
            ->columns([
                TextColumn::make('kelompok.nm_kelompok')
                ->label('Kelompok'),
                TextColumn::make('nama')
                ->label('Nama Lengkap'),
                TextColumn::make('jk')
                ->label('L/P'),
                TextColumn::make('status')
                ->label('Status'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Tables\Actions\Action::make('Apply')
                    ->icon('heroicon-s-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Data')
                    ->modalDescription('Apakah kamu yakin data yang diregistrasi adalah benar Muda-mudimu ?')
                    ->modalSubmitActionLabel('Ya Benar')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (Registrasi $record) {
                       if (DB::table('mudamudis')->where('nama', $record->nama)->where('tgl_lahir', $record->tgl_lahir)->exists()) {
                            return Notification::make()
                                ->danger()
                                ->title('Data Sudah Ada Didatabase, Silahkan Cek Kembali!')
                                ->send();
                        } else {
                            $data = $record->toArray();
                            Mudamudi::create($data);
                            Riwayat::create([
                                'daerah_id' => $data['daerah_id'],
                                'desa_id' => $data['desa_id'],
                                'kelompok_id' => $data['kelompok_id'],
                                'nama' => $data['nama'],
                                'nm_user' => auth()->user()->name,
                                'action' => 'Apply',
                            ]);

                            $record->delete();
                            return Notification::make()
                                ->success()
                                ->title('Data Berhasil Ditambahkan')
                                ->send();
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Reject')
                    ->modalHeading('Hapus Data Registrasi')
                    ->modalDescription('Apakah kamu yakin ingin menghapus data registrasi ini ?')
                    ->modalSubmitActionLabel('Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                // ...
            ])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum ada yang registrasi');
    }

    public static function getEloquentQuery(): Builder
    {
        $role = Auth::user()->roles;
        $daerah = '';
        $desa = '';
        $kelompok = '';
        // Transalasi String Field Detail yang ada di User menjadi Id 
        if ($role[0]->name == 'MM Daerah') {
            $daerah = Daerah::query()->where('nm_daerah', '=', auth()->user()->detail)->first(['id']);
            return parent::getEloquentQuery()->where('daerah_id', $daerah->id);
        } elseif ($role[0]->name == 'MM Desa') {
            $desa = Desa::query()->where('nm_desa', '=', auth()->user()->detail)->first(['id', 'daerah_id']);
            return parent::getEloquentQuery()->where('daerah_id', $desa->daerah_id)->where('desa_id', $desa->id);
        } elseif ($role[0]->name == 'MM Kelompok') {
            $kelompok = Kelompok::query()->where('nm_kelompok', '=', auth()->user()->detail)->first(['id', 'desa_id']);
            $desa = Desa::query()->where('id', '=', $kelompok->desa_id)->first(['id', 'daerah_id']);
            return parent::getEloquentQuery()->where('daerah_id', $desa->daerah_id)->where('desa_id', $desa->id)->where('kelompok_id', $kelompok->id);
        }
    }

    public function render()
    {
        return view('livewire.registrasi-kegiatan-table');
    }
}
