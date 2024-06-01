<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = ['tea', 'cola', 'cocktail', 'soda', 'beer'];

        foreach ($products as $product) {
            Item::create([
                'name' => $product,
                'image' => 'https://cdn.pixabay.com/photo/2015/07/02/20/37/cup-829527_1280.jpg',
                'price' => 1200,
                'original_price' => 700,
                'category_id' => 1,
                'qty' => 20,
            ]);
        }

        Item::create([
            'name' => 'rice',
            'image' => 'https://cdn.pixabay.com/photo/2015/07/02/20/37/cup-829527_1280.jpg',
            'price' => 1200,
            'original_price' => 700,
            'category_id' => 2,
        ]);
    }
}
