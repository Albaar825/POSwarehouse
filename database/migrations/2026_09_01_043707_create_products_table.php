<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('sku')->unique();

            $table->string('name');

            $table->string('image')->nullable();

            $table->unsignedBigInteger('purchase_price')->default(0);

            $table->unsignedBigInteger('price')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Total stok produk
            |--------------------------------------------------------------------------
            | Nilai ini merupakan hasil penjumlahan seluruh stok variant.
            |
            | Contoh:
            | Hitam M  = 10
            | Hitam L  = 15
            | Putih M  = 20
            |
            | Total = 45
            |--------------------------------------------------------------------------
            */
            $table->integer('stock')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Minimum stok tetap berada di level produk
            |--------------------------------------------------------------------------
            */
            $table->integer('min_stock')->default(0);

            $table->string('unit')->default('pcs');

            /*
            |--------------------------------------------------------------------------
            | Dynamic Variant Groups
            |--------------------------------------------------------------------------
            |
            | Contoh:
            |
            | [
            |     {
            |         "name": "Warna",
            |         "type": "parent",
            |         "values": [
            |             {
            |                 "name": "Hitam",
            |                 "image": "products/variants/hitam.jpg"
            |             },
            |             {
            |                 "name": "Putih",
            |                 "image": "products/variants/putih.jpg"
            |             }
            |         ]
            |     },
            |     {
            |         "name": "Ukuran",
            |         "type": "child",
            |         "values": [
            |             {
            |                 "name": "M",
            |                 "image": null
            |             },
            |             {
            |                 "name": "L",
            |                 "image": null
            |             }
            |         ]
            |     }
            | ]
            |
            |--------------------------------------------------------------------------
            */
            $table->json('variant_groups')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
