<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Customers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            
            // Primary key
            $table->increments('id');
            
            // Foreign keys
            $table->integer('user_id')->unique()->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            
            // Basic info
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 50);
            
            // Address
            $table->string('city', 50);
            $table->string('street', 50);
            $table->string('building', 50);
            $table->string('entrance', 50)->nullable();
            $table->string('apartment', 50)->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            
            // Credit card
            $table->string('credit_card_number')->nullable();
            $table->string('valid_until_month')->nullable();
            $table->string('valid_until_year')->nullable();
            $table->string('security_code')->nullable();

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
        Schema::drop('customers');
    }
}
