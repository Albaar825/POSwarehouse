<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // kasir yang input
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('paid')->default(0);
            $table->unsignedBigInteger('change')->default(0);
            $table->enum('payment_method', ['cash', 'qris'])->default('cash');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
