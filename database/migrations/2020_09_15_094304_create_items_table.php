<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigInteger('id')->index();
            $table->foreignId('warehouse_system_id')->constrained();
            $table->timestamp('available_from_date')->index();
            $table->string('sku');
            $table->string('product_url');
            $table->float('quantity', 8, 2)->index();
            $table->string('upc');
            $table->float('unit_price', 8, 2)->index();
            $table->float('recommended_sales_price', 8, 2)->index();
            $table->string('product_name');
            $table->timestamps();
            $table->primary(['id', 'warehouse_system_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
