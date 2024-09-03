<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = array(
            array('name' => '💰 Business', 'icon' => ''),
            array('name' => '🍎 Santé', 'icon' => ''),
            array('name' => '📚 Développement', 'icon' => ''),
            array('name' => '🎨 Arts', 'icon' => ''),
            array('name' => '🎸 Musique', 'icon' => ''),
            array('name' => '🛍️ E-commerce', 'icon' => ''),
            array('name' => '🏋️‍♀️ Sport', 'icon' => ''),
            array('name' => '👩‍💻 Technologies', 'icon' => ''),
            array('name' => '🔥 Autres', 'icon' => ''),
        );

        foreach ($categories as $category) {
            $check = Category::where('name', $category['name'])->first();
            if (!$check) {
                Category::create([
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                ]);
            }
        }

        $this->command->info('Caégories ajoutées.');
    }
}
