<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Membuat permission untuk menu panel App dan memberi default ke role MM.
 * Aman dijalankan berulang kali (idempotent). Tidak menghapus permission yang sudah diberikan manual.
 */
class ShieldAppPermissionSeeder extends Seeder
{
    private const PREFIXES = [
        'view', 'view_any', 'create', 'update', 'restore', 'restore_any',
        'replicate', 'reorder', 'delete', 'delete_any', 'force_delete', 'force_delete_any',
    ];

    private const RESOURCES = [
        'mudamudiapp', 'pengurus::app', 'desa::app', 'kelompok::app',
        'kegiatan', 'laporan::kegiatan', 'registrasi', 'uang::kas',
    ];

    private const WIDGETS = [
        'widget_CountWidget', 'widget_JkWidget', 'widget_JenjangWidget', 'widget_StatusWidget',
        'widget_SensusChart', 'widget_RiwayatUpdate', 'widget_ArusKeluar',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Buat semua permission
        foreach (self::RESOURCES as $resource) {
            foreach (self::PREFIXES as $prefix) {
                Permission::firstOrCreate(['name' => "{$prefix}_{$resource}", 'guard_name' => 'web']);
            }
        }
        foreach (self::WIDGETS as $widget) {
            Permission::firstOrCreate(['name' => $widget, 'guard_name' => 'web']);
        }

        // 2. Default untuk semua role MM
        $common = [
            ...$this->crud('mudamudiapp'),
            ...$this->crud('pengurus::app'),
            ...$this->crud('uang::kas'),
            ...$this->crud('laporan::kegiatan'),
            'view_any_kegiatan', 'view_kegiatan', 'create_kegiatan', 'delete_kegiatan', 'delete_any_kegiatan',
            'view_any_registrasi',
            'widget_CountWidget', 'widget_JkWidget', 'widget_JenjangWidget', 'widget_StatusWidget',
        ];

        // Widget yang dulu disembunyikan untuk MM Kelompok
        $nonKelompokWidgets = ['widget_SensusChart', 'widget_RiwayatUpdate', 'widget_ArusKeluar'];

        $map = [
            'MM Daerah'   => [...$common, ...$nonKelompokWidgets, ...$this->crud('desa::app'), ...$this->crud('kelompok::app')],
            'MM Desa'     => [...$common, ...$nonKelompokWidgets],
            'MM Kelompok' => $common,
        ];

        foreach ($map as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->givePermissionTo($permissions); // menambah, tidak menghapus yang sudah ada
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function crud(string $resource): array
    {
        return [
            "view_any_{$resource}", "view_{$resource}", "create_{$resource}",
            "update_{$resource}", "delete_{$resource}", "delete_any_{$resource}",
        ];
    }
}
