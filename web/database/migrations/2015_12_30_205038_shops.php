<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Shops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            
            // Primary key
            $table->increments('id');
            
            // Foreign keys
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            
            // Basic info
            $table->string('name', 50);
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('company_name', 50);
            $table->string('company_id', 50);
            $table->string('phone', 50);
            $table->string('mobile', 50);
            
            // Address
            $table->string('city', 50);
            $table->string('street', 50);
            $table->string('building', 50);
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            
            // Bank account
            $table->string('bank_name');
            $table->string('bank_branch');
            $table->string('bank_account_number');

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
        Schema::drop('shops');
    }
}
