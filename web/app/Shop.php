<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    
    use SoftDeletes;

	// Variables

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'first_name',
        'last_name',
        'company_name',
        'company_id',
        'phone',
        'mobile',
        'city',
        'street',
        'building',
        'latitude',
        'longitude',
        'bank_name',
        'bank_branch',
        'bank_account_number'
    ];

    protected $hidden = ['user_id', 'deleted_at'];

    protected $dates = ['deleted_at'];

    // Functions

    public function proposals()
    {
    	return $this->hasMany('App\OrderProposal')->join('orders', 'orders.id', '=', 'order_proposals.order_id')->select('order_proposals.id', 'order_proposals.order_id', 'order_proposals.shop_id', 'order_proposals.shop_notes', 'order_proposals.delivery_time', 'order_proposals.price', 'order_proposals.delivery_price', 'order_proposals.status', 'order_proposals.proposed_at', 'order_proposals.accepted_at', 'order_proposals.declined_at', 'order_proposals.processed_at', 'order_proposals.created_at', 'customer_id', 'customer_notes', 'city', 'street', 'building', 'entrance', 'apartment', 'latitude', 'longitude', 'proposal_id')->whereRaw('order_proposals.status <= 3 AND orders.status <= 3 AND orders.deleted_at IS NULL')->orderBy('orders.id', 'desc');
    }

    public function proposalsSummary()
    {
        return $this->hasMany('App\OrderProposal')->whereRaw('status BETWEEN 5 AND 6')->orderBy('id', 'desc');
    }

    public function proposalsAll()
    {
        return $this->hasMany('App\OrderProposal')->orderBy('id', 'desc');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function feedbacks()
    {
        return $this->hasMany('App\Feedback')->orderBy('created_at', 'desc');
    }

    public static function geoSearch($latitude, $longitude, $distance)
    {
        
        return \DB::table('shops')
            ->select('*', \DB::raw('3956 * 2 * ASIN(SQRT( POWER(SIN((' . $latitude . ' -
            abs( 
            shops.latitude)) * pi()/180 / 2),2) + COS(' . $latitude . ' * pi()/180 ) * COS( 
            abs
            (shops.latitude) *  pi()/180) * POWER(SIN((' . $longitude . ' - shops.longitude) *  pi()/180 / 2), 2) )) * 1.61
            as distance'))
            ->having('distance', '<', $distance)
            ->orderBy('distance');

        return \DB::select('
            SELECT *, 
            3956 * 2 * ASIN(SQRT( POWER(SIN((? -
            abs( 
            dest.latitude)) * pi()/180 / 2),2) + COS(? * pi()/180 ) * COS( 
            abs
            (dest.latitude) *  pi()/180) * POWER(SIN((? - dest.longitude) *  pi()/180 / 2), 2) )) * 1.61
            as distance 
            FROM shops dest having distance < ? ORDER BY distance',
            [$latitude, $latitude, $longitude, $distance]
        );
    }

}
