<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = array(
            array('name' => 'admin', 'description' => 'Consulter le tableau de bord'),
            array('name' => 'profils', 'description' => 'Gérer les profils exportés depuis linkedin'),
            array('name' => 'entreprises', 'description' => 'Consulter les entreprises'),
            array('name' => 'clients', 'description' => 'Gérer les clients'),
            array('name' => 'timesheet', 'description' => 'Saisir mes timesheets'),
            array('name' => 'timesheet-admin', 'description' => 'Voir les timesheets'),
            array('name' => 'edit_timesheet', 'description' => 'Valider ou refuser les timesheets'),
            array('name' => 'projects', 'description' => 'Gérer les projets'),
            array('name' => 'candidats', 'description' => 'Gérer les candidatures'),
            array('name' => 'offres', 'description' => 'Gérer les offres'),
            array('name' => 'users', 'description' => 'Gérer les utilisateurs'),
        );

        // delete all permissions
        // Permission::where('name', 'like', '%')->delete();
        foreach ($permissions as $permission) {
            $check = Permission::where('name', $permission['name'])->first();
            if (!$check) {
                Permission::create([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                ]);
            }
        }

        $this->command->info('Permissions ajoutées.');
    }
}
