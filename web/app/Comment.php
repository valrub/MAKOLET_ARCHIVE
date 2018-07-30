<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    
	use SoftDeletes;

	protected $fillable = [
		'level',
		'comment',
		'customer_id',
		'shop_id',
		'order_id'
    ];

    protected $dates = ['deleted_at'];

}
