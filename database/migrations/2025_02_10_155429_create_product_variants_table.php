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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreignId('color_id');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreignId('size_id');
            $table->foreign('size_id')->references('id')->on('sizes');
            $table->decimal('price',6,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
