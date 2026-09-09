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

            $table->enum('type', [
                'sale',
                'open_invoice',
            ])->default('sale');

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedBigInteger('total')->default(0);

            $table->unsignedBigInteger('paid')->default(0);

            $table->unsignedBigInteger('change')->default(0);

            $table->enum('payment_method', [
                'cash',
                'qris',
                'credit',
            ])->default('cash');

            $table->enum('status', [
                'paid',
                'partial',
                'on_hold',
                'cancelled',
            ])->default('paid');

            $table->date('due_date')->nullable();

            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
