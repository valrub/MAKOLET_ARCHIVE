<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{

    use SoftDeletes;

	protected $fillable = [
		'user_id',
		'first_name',
		'last_name',
		'phone',
		'city',
		'street',
		'building',
		'entrance',
		'apartment',
		'latitude',
		'longitude',
		'credit_card_number',
		'valid_until_month',
		'valid_until_year',
		'security_code',
    ];

	protected $hidden = [
        'user_id',
        'credit_card_number',
        'valid_until_month',
        'valid_until_year',
        'security_code',
        'tranzilatk',
        'expmonth',
        'expyear',
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function orders()
    {
        return $this->hasMany('App\Order')->orderBy('id', 'desc');
    }

    public function ordersSummary()
    {
    	return $this->hasMany('App\Order')->whereRaw('status BETWEEN 5 AND 6')->orderBy('id', 'desc');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

}
