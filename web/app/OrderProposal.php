<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProposal extends Model
{
    
	// Variables

    protected $fillable = ['order_id', 'shop_id', 'status'];

    protected $hidden = ['updated_at', 'deleted_at'];

    // Functions

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function shop()
    {
    	return $this->belongsTo('App\Shop')->withTrashed();
    }

}
