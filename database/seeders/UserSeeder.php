<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $admin = Role::create(['name' => 'Admin']);
        // $daerah = Role::create(['name' => 'MM Daerah']);
        // $desa = Role::create(['name' => 'MM Desa']);
        // $kelompok = Role::create(['name' => 'MM Kelompok']);
        $pengurusdaerah = Role::create(['name' => 'Pengurus Daerah']);

        // Permission::create([
        //     'name' => 'tambah mm',
        //     'guard_name' => 'web',
        // ]);

        // Permission::create([
        //     'name' => 'edit mm',
        //     'guard_name' => 'web',
        // ]);

        // Permission::create([
        //     'name' => 'hapus mm',
        //     'guard_name' => 'web',
        // ]);
        // Permission::create([
        //     'name' => 'edit pengurus',
        //     'guard_name' => 'web',
        // ]);
        
        // Permission::create([
        //     'name' => 'hapus pengurus',
        //     'guard_name' => 'web',
        // ]);

        // // Admin
        // User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'adminkarawangtimur@kmd.com',
        //     'detail' => 'Admin',
        //     'is_admin' => true
        // ])->assignRole($admin);

        // // MM Daerah
        // User::factory()->create([
        //     'name' => 'MM Kartim',
        //     'email' => 'mmkartim@kmd.com',
        //     'detail' => 'Karawang Timur',
        //     'is_admin' => false
        // ])->assignRole($daerah);

        // // MM Desa
        // User::factory()->create([
        //     'name' => 'MM Klari',
        //     'email' => 'mmklari@kmd.com',
        //     'detail' => 'Klari',
        //     'is_admin' => false
        // ])->assignRole($desa);

        // User::factory()->create([
        //     'name' => 'MM Lemah Mulya',
        //     'email' => 'mmlemahmulya@kmd.com',
        //     'detail' => 'Lemah Mulya',
        //     'is_admin' => false
        // ])->assignRole($desa);

        // User::factory()->create([
        //     'name' => 'MM Adiarsa Timur',
        //     'email' => 'mmadiarsatimur@kmd.com',
        //     'detail' => 'Adiarsa Timur',
        //     'is_admin' => false
        // ])->assignRole($desa);

        // User::factory()->create([
        //     'name' => 'MM Tunggak Jati',
        //     'email' => 'mmtunggakjati@kmd.com',
        //     'detail' => 'Tunggak Jati',
        //     'is_admin' => false
        // ])->assignRole($desa);

        // // MM Kelompok
        // User::factory()->create([
        //     'name' => 'MM Cirejag',
        //     'email' => 'mmcirejag@kmd.com',
        //     'detail' => 'Cirejag',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Belendung',
        //     'email' => 'mmbelendung@kmd.com',
        //     'detail' => 'Belendung',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Cibalongsari 1',
        //     'email' => 'mmcibalongsari1@kmd.com',
        //     'detail' => 'Cibalongsari 1',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Cibalongsari 2',
        //     'email' => 'mmcibalongsari2@kmd.com',
        //     'detail' => 'Cibalongsari 2',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Pancawati',
        //     'email' => 'mmpancawati@kmd.com',
        //     'detail' => 'Pancawati',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Kalimulya',
        //     'email' => 'mmkalimulya@kmd.com',
        //     'detail' => 'Kalimulya',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // // Kelompok Desa LM
        // User::factory()->create([
        //     'name' => 'MM Tamiang 1',
        //     'email' => 'mmtamiang1@kmd.com',
        //     'detail' => 'Tamiang 1',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Tamiang 2',
        //     'email' => 'mmtamiang2@kmd.com',
        //     'detail' => 'Tamiang 2',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM CKM',
        //     'email' => 'mmckm@kmd.com',
        //     'detail' => 'CKM',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Tipar',
        //     'email' => 'mmtipar@kmd.com',
        //     'detail' => 'Tipar',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Anggadita',
        //     'email' => 'mmanggadita@kmd.com',
        //     'detail' => 'Anggadita',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM HNH',
        //     'email' => 'mmhnh@kmd.com',
        //     'detail' => 'HNH',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // // MM Kelompok Adiarsa Timur

        // User::factory()->create([
        //     'name' => 'MM Karawang Kota 1',
        //     'email' => 'mmkarawangkota1@kmd.com',
        //     'detail' => 'Karawang Kota 1',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Karawang Kota 2',
        //     'email' => 'mmkarawangkota2@kmd.com',
        //     'detail' => 'Karawang Kota 2',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Babakan Jati',
        //     'email' => 'mmbabakanjati@kmd.com',
        //     'detail' => 'Babakan Jati',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Wirasaba',
        //     'email' => 'mmwirasaba@kmd.com',
        //     'detail' => 'Wirasaba',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Wadas',
        //     'email' => 'mmwadas@kmd.com',
        //     'detail' => 'Wadas',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // // MM Kelompok Tunggak Jati
        // User::factory()->create([
        //     'name' => 'MM Tanjung Baru Barat',
        //     'email' => 'mmtanjungbarubarat@kmd.com',
        //     'detail' => 'Tanjung Baru Barat',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Tanjung Baru Timur',
        //     'email' => 'mmtanjungbarutimur@kmd.com',
        //     'detail' => 'Tanjung Baru Timur',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Tunggak Jati',
        //     'email' => 'mmtunggakjati2@kmd.com',
        //     'detail' => 'Tunggak Jati',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Kalangsuria',
        //     'email' => 'mmkalangsuria@kmd.com',
        //     'detail' => 'Kalangsuria',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        // User::factory()->create([
        //     'name' => 'MM Pakis Jaya',
        //     'email' => 'mmpakisjaya@kmd.com',
        //     'detail' => 'Pakis Jaya',
        //     'is_admin' => false
        // ])->assignRole($kelompok);

        User::factory()->create([
            'name' => 'Penerobos Daerah',
            'email' => 'penerobosdaerah@kmd.com',
            'detail' => 'Penerobos',
            'is_admin' => false
        ])->assignRole($pengurusdaerah);
    }
}
