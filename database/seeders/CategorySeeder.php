<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Jaket, Mantel, & Rompi',
                'description' => 'Produk Jaket',
            ],
            [
                'name' => 'Hoodie & Sweatshirt',
                'description' => 'Produk Hoodie',
            ],
            [
                'name' => 'Pakaian Anak Laki-Laki',
                'description' => 'Produk Anak Laki-Laki',
            ],
            [
                'name' => 'Atasan',
                'description' => 'Produk Kaos',
            ],
            [
                'name' => 'Celana Panjang',
                'description' => 'Produk Celana',
            ],
            [
                'name' => 'Sweater & Cardigan',
                'description' => 'Produk sweater',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
