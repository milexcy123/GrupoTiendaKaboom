<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->enum('delivery_type', ['home', 'pickup']);
            $table->enum('payment_method', ['cash_on_delivery', 'pay_at_store']);
            $table->decimal('total', 10, 2);
            $table->enum('status', [
                'received',
                'preparing',
                'ready_for_pickup',
                'on_the_way',
                'delivered',
                'canceled',
            ])->default('received');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};