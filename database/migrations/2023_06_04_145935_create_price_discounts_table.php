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
        Schema::create('price_discounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('amount');

            // oneOf: percentage, fixed
            $table->string('type')->default('percentage');
            // oneOf: product, category, global
            $table->string('target_type')->default('product');
            $table->integer('target_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_discounts');
    }
};
