<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            
            // Primary key
            $table->increments('id');
            
            // Foreign keys
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            
            // Order details
            $table->string('customer_notes')->nullable();
            
            // Address
            $table->string('city');
            $table->string('street');
            $table->string('building');
            $table->string('entrance')->nullable();
            $table->string('apartment')->nullable();
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            
            // Order status
            $table->integer('status')->default(1);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
