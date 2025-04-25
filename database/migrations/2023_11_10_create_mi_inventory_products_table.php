<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mi_inventory_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('stock')->default(0);
            $table->decimal('price', 15, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('inventory_id')->references('id')->on('mi_inventories')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('ec_products')->onDelete('cascade');
            
            $table->unique(['inventory_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mi_inventory_products');
    }
};