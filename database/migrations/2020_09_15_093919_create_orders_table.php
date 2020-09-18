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
            $table->id();
            $table->foreignId('order_system_id')->constrained();
            $table->string('external_id')->index(); // the order id given by the exernal order system
            $table->timestamp('shipping_date')->index();
            $table->string('retailer_carrier_code');
            $table->string('retailer_carrier_service_code');
            $table->string('billing_city');
            $table->string('billing_address');
            $table->string('billing_country');
            $table->string('billing_state');
            $table->string('shipping_city');
            $table->string('shipping_address');
            $table->string('shipping_country');
            $table->string('shipping_state');
            $table->string('status')->index();
            $table->string('submission_result')->nullable();
            $table->timestamps();
            $table->unique(['order_system_id', 'external_id']);
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
