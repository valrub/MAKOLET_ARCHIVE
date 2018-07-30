<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    
	// Variables

	protected $table = 'order_goods';

    protected $fillable = ['order_id', 'name', 'quantity'];

    protected $hidden = ['order_id', 'created_at', 'updated_at', 'deleted_at', 'quantity'];

}
