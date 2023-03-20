<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductHamperLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_hamper_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreignUuid('product_id')->constrained('products');
            $table->foreignUuid('product_hamper_id')->constrained('product_hampers');
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_hamper_lines');
    }
}
