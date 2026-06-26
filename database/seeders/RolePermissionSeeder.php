<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $permissions = [
            'aduan.view_any', 'aduan.view', 'aduan.create', 'aduan.update', 'aduan.delete',
            'aduan.assign',     // tugaskan juruteknik
            'aduan.verify',     // sahkan selesai (penyelia)
            'aduan.approve',    // lulus akhir (pengurus)
            'user.manage',      // urus pengguna
            'laporan.view',     // lihat laporan
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'Pentadbir']);
        $admin->syncPermissions(Permission::all());

        $pengurus = Role::firstOrCreate(['name' => 'Pengurus Operasi']);
        $pengurus->syncPermissions([
            'aduan.view_any', 'aduan.view', 'aduan.approve', 'laporan.view',
        ]);

        $penyelia = Role::firstOrCreate(['name' => 'Penyelia Penyelenggaraan']);
        $penyelia->syncPermissions([
            'aduan.view_any', 'aduan.view', 'aduan.create', 'aduan.update',
            'aduan.assign', 'aduan.verify', 'laporan.view',
        ]);

        $juruteknik = Role::firstOrCreate(['name' => 'Juruteknik']);
        $juruteknik->syncPermissions([
            'aduan.view', 'aduan.update',  // hanya aduan sendiri
        ]);
    }
}
