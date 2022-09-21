<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreignUuid('purchase_id')->constrained();
            $table->uuid('item_id');
            $table->string('item_name');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_line_items');
    }
}
