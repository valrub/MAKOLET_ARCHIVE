<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_proposals', function (Blueprint $table) {
            
            // Primary key
            $table->increments('id');
            
            // Foreign keys
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');
            
            $table->integer('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops');
            
            // Proposal details
            $table->string('shop_notes')->nullable();
            $table->timestamp('delivery_time')->nullable();
            $table->decimal('price', 5, 2)->nullable();
            
            // Status
            // 1 - Proposal requested
            // 2 - Proposal recieved
            // 3 - Proposal accepted
            // 4 - Proposal declined
            // 5 - Proposal confirmed and in proccesing
            $table->integer('status')->default(1);

            // Timestamps
            $table->timestamp('proposed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

        });

        Schema::table('orders', function ($table) {

            // Foreign keys
            $table->integer('proposal_id')->nullable()->unsigned();
            $table->foreign('proposal_id')->references('id')->on('order_proposals');
        
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('orders', function ($table) {

            // Foreign keys
            $table->dropForeign('orders_proposal_id_foreign');
        
        });

        Schema::drop('order_proposals');
    }
}
