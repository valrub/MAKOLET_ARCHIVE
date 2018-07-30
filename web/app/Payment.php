<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    
	// Variables
	
	protected $fillable = ['customer_id', 'tempref', 'response', 'sum'];

	// Functions

	public function orders()
	{
		return $this->hasMany('App/Order');
	}

}
