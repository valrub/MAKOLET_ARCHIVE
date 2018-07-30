<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    
	// Variables

	protected $table = 'shop_feedbacks';

	protected $fillable = ['customer_id', 'shop_id', 'order_id', 'comment', 'score'];

	protected $hidden = ['shop_id', 'updated_at', 'deleted_at'];

	public function customer()
    {
        return $this->belongsTo('App\Customer')->withTrashed();
    }

    public function shop()
    {
        return $this->belongsTo('App\Shop')->withTrashed();
    }

}
