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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('article');
            $table->string('brand');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->integer('minimal_quantity');
            $table->string('currency');
            $table->decimal('price', 8, 2);
            $table->string('bkey');
            $table->string('akey');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
