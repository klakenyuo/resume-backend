<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Etape;

class EtapeSeeder extends Seeder
{
    public function run()
    {
        $etapes = array(
            array('name' => 'pre-qualification', 'description' => 'Pre-qualification'),
            array('name' => 'entretien-technique', 'description' => 'Entretien technique'),
            array('name' => 'entretien-d-offre', 'description' => 'Entretien d’offre'),
            array('name' => 'lettre-offre', 'description' => 'Lettre offre'),
            array('name' => 'signature-offre', 'description' => 'Signature offre'),
            array('name' => 'contrat-de-travail', 'description' => 'Contrat de travail'),
            array('name' => 'autorisation-de-travail', 'description' => 'Autorisation de travail'),
            array('name' => 'demande-visa', 'description' => 'Demande visa'),
            array('name' => 'mise-a-jour-cv', 'description' => 'Mise à jour CV'),
            array('name' => 'transmission-des-cv-au-clients', 'description' => 'Transmission des CV au clients'),
            array('name' => 'validation-titre-de-sejour', 'description' => 'Validation titre de sejour'),
            array('name' => 'numero-de-securite-sociale', 'description' => 'Numéro de sécurité sociale'),     
            array('name' => 'ouverture-de-compte', 'description' => 'Ouverture de compte'),
            array('name' => 'entretien-client', 'description' => 'Entretien client'),
            array('name' => 'signature-du-contrat-de-travail', 'description' => 'Signature du contrat de travail'),
        );

        // delete all etapes
        // etape::where('name', 'like', '%')->delete();
        foreach ($etapes as $etape) {
            $check = Etape::where('name', $etape['name'])->first();
            if (!$check) {
                Etape::create([
                    'name' => $etape['name'],
                    'description' => $etape['description'],
                ]);
            }
        }

        $this->command->info('Etapes ajoutées.');
    }
}
