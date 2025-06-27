<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('temp_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('reference_number')->unique();
            $table->json('cart'); // Simpan keranjang belanja sebagai JSON
            $table->string('payment_method');
            $table->enum('purchase_type', ['pickup', 'delivery']);
            $table->unsignedBigInteger('address_id')->nullable();
            $table->timestamps();
            $table->foreign('address_id')->references('id')->on('user_addresses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_orders');
    }
};
