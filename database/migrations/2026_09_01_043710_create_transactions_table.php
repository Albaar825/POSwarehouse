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

            // Invoice
            $table->string('invoice_number')->unique();

            // Kasir
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Customer
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Total transaksi
            $table->unsignedBigInteger('total')
                ->default(0);

            // Total yang sudah dibayar
            $table->unsignedBigInteger('paid')
                ->default(0);

            // Kembalian
            $table->unsignedBigInteger('change')
                ->default(0);

            // Metode pembayaran
            $table->enum('payment_method', [
                'cash',
                'qris',
                'credit',
            ])->default('cash');

            // Status invoice
            $table->enum('status', [
                'paid',
                'partial',
                'on_hold',
                'cancelled',
            ])->default('paid');

            // Jatuh tempo pembayaran
            $table->date('due_date')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
