<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      app()[PermissionRegistrar::class]->forgetCachedPermissions();

      // create permissions
      Role::create(['name' => 'owner']); // proprietário adm fusion
      Role::create(['name' => 'admin']); // admininistrador fusion
      Role::create(['name' => 'partner']); // parceiro - proprietário de imóveis (coworking, etc)
      Role::create(['name' => 'customer']); // cliente - especialista
      Role::create(['name' => 'patient']); // cliente - especialista

      // create roles and assign created permissions
      // $superAdmin->givePermissionTo(Permission::all());
      // $admin->givePermissionTo([
      //   'create units',
      //   'create photos',
      //   'create users',
      //   'create addresses',
      // ]);

      // $customer->givePermissionTo([
      //   'create photos',
      //   'create addresses',
      // ]);
    }
}
