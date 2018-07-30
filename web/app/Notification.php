<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    
	use SoftDeletes;

	protected $fillable = ['user_id', 'order_id', 'status', 'message'];

}
