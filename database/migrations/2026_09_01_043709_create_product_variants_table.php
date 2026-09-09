<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Dynamic attributes
            |--------------------------------------------------------------------------
            |
            | Contoh:
            |
            | [
            |     "Hitam",
            |     "M",
            |     "Cotton"
            | ]
            |
            | Urutannya mengikuti urutan variant_groups pada product.
            |
            |--------------------------------------------------------------------------
            */
            $table->json('attributes');

            /*
            |--------------------------------------------------------------------------
            | Harga variant
            |--------------------------------------------------------------------------
            |
            | NULL = menggunakan harga utama product
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('price')->nullable();

            /*
            |--------------------------------------------------------------------------
            | SKU setiap kombinasi
            |--------------------------------------------------------------------------
            */
            $table->string('sku_variant')->unique();

            /*
            |--------------------------------------------------------------------------
            | Stok kombinasi
            |--------------------------------------------------------------------------
            */
            $table->integer('stock')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
