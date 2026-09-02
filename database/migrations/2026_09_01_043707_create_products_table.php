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
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('image')->nullable(); // path gambar produk
            $table->unsignedBigInteger('purchase_price')->default(0);
            $table->unsignedBigInteger('price')->default(0);
            $table->integer('stock')->default(0); // cache total, sumber asli: sum(product_variants.stock)
            $table->integer('min_stock')->default(0);
            $table->string('unit')->default('pcs');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
