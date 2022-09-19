<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('order_number');
            $table->foreignUuid('order_source_id')->constrained();
            $table->foreignUuid('branch_id')->constrained();
            $table->boolean('reseller_order')->default(false);
            $table->foreignUuid('reseller_id')->nullable()->constrained();
            $table->string('customer_name');
            $table->unsignedInteger('percentage_discount')->default(0);
            $table->unsignedBigInteger('total_discount')->default(0);
            $table->unsignedBigInteger('total_line_items_quantity')->default(0);
            $table->unsignedBigInteger('total_line_items_price')->default(0);
            $table->unsignedBigInteger('total_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
