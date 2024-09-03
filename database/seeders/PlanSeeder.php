<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $plans = array(
            array('name' => 'Lite', 'description' => 'Plan lite', 'amount' => 49, 'maxMembers' => 100),
            array('name' => 'Basic', 'description' => 'Plan de base', 'amount' => 99, 'maxMembers' => 1000),
            array('name' => 'Pro', 'description' => 'Plan professionnel', 'amount' => 149, 'maxMembers' => 0),
        );

        foreach ($plans as $plan) {
            $check = Plan::where('name', $plan['name'])->first();
            if (!$check) {
                Plan::create($plan);
            }
        }

        $this->command->info('Plans ajoutées.');
    }
}
