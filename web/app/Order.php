<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    
    use SoftDeletes;

	// Variables

    protected $fillable = ['customer_id', 'customer_notes', 'city', 'street', 'building', 'entrance', 'apartment', 'latitude', 'longitude'];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    // Functions

    public function goods()
    {
        return $this->hasMany('App\OrderItem');
    }

    public function proposal()
    {
        return $this->belongsTo('App\OrderProposal');
    }

    public function proposals()
    {
    	return $this->hasMany('App\OrderProposal');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer')->withTrashed();
    }

    public function feedback()
    {
        return $this->hasOne('App\Feedback');
    }

    public static function byShopId($shop_id) {

        return \DB::table('orders')
            ->join('order_proposals', 'orders.id', '=', 'order_proposals.order_id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select('orders.id', 'order_proposals.shop_id', 'order_proposals.shop_notes', 'orders.customer_id', 'customers.first_name', 'customers.last_name', 'customers.phone', 'orders.customer_notes', 'orders.city', 'orders.street', 'orders.building', 'orders.entrance', 'orders.apartment', 'orders.latitude', 'orders.longitude', 'orders.status', 'order_proposals.delivery_time', 'order_proposals.price', 'order_proposals.delivery_price', 'order_proposals.proposed_at', 'order_proposals.accepted_at', 'order_proposals.declined_at', 'order_proposals.processed_at', 'orders.created_at', 'orders.updated_at')
            ->whereRaw('order_proposals.shop_id = ' . $shop_id . ' AND (orders.proposal_id IS NULL OR orders.proposal_id = order_proposals.id)')
            ->orderBy('orders.id', 'desc');

        /*
        return \DB::select(
            '
            SELECT orders.id, orders.customer_id, order_proposals.shop_id, orders.customer_notes, order_proposals.shop_notes, orders.city, orders.street, orders.building, orders.entrance, orders.apartment, orders.latitude, orders.longitude, orders.status, order_proposals.delivery_time, order_proposals.price, order_proposals.proposed_at, order_proposals.accepted_at, order_proposals.declined_at, order_proposals.processed_at, orders.created_at, orders.updated_at
            FROM orders
            INNER JOIN order_proposals ON orders.id = order_proposals.order_id
            WHERE order_proposals.shop_id = ?
            AND (orders.proposal_id IS NULL OR orders.proposal_id = order_proposals.id)
            ORDER BY orders.id DESC
            '
        , [$shop_id]);

        */
    }

    public static function summaryByShopId($shop_id, $page, $order_by = 'orders.id', $order_type = 'desc')
    {
        return \DB::table('orders')
            ->join('order_proposals', 'orders.id', '=', 'order_proposals.order_id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('shops', 'order_proposals.shop_id', '=', 'shops.id')
            ->select('orders.id', 'order_proposals.shop_id', \DB::raw('shops.name as shop_name'), 'order_proposals.shop_notes', 'orders.customer_id', 'customers.first_name', 'customers.last_name', 'customers.phone', 'orders.customer_notes', 'orders.city', 'orders.street', 'orders.building', 'orders.entrance', 'orders.apartment', 'orders.latitude', 'orders.longitude', 'orders.status', 'order_proposals.delivery_time', 'order_proposals.price', 'order_proposals.delivery_price', 'order_proposals.proposed_at', 'order_proposals.accepted_at', 'order_proposals.declined_at', 'order_proposals.processed_at', 'orders.created_at', 'orders.updated_at')
            ->whereRaw('order_proposals.shop_id = ' . $shop_id . ' AND orders.status BETWEEN 5 AND 6 AND (orders.proposal_id IS NULL OR orders.proposal_id = order_proposals.id) AND orders.created_at > (DATE_SUB(DATE_FORMAT(CURDATE(), \'%Y-%m-01\'), INTERVAL ' . (($page - 1) * 2 + 1) . ' MONTH)) AND orders.created_at < (DATE_SUB(DATE_FORMAT(CURDATE(), \'%Y-%m-01\'), INTERVAL ' . (($page - 1) * 2 - 1) . ' MONTH))')
            ->orderByRaw('TIME_FORMAT(orders.created_at, "%Y%m") DESC, orders.id DESC');
            //->orderByRaw('TIME_FORMAT(orders.created_at, "%Y%m") DESC, customers.first_name, customers.last_name, order_proposals.price DESC, TIME_FORMAT(orders.created_at, "%Y%m%d") DESC');
    }

    public static function summaryByCustomerId($customer_id, $page, $order_by = 'orders.id', $order_type = 'desc') 
    {
        return \DB::table('orders')
            ->join('order_proposals', 'orders.id', '=', 'order_proposals.order_id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('shops', 'order_proposals.shop_id', '=', 'shops.id')
            ->select('orders.id', 'order_proposals.shop_id', \DB::raw('shops.name as shop_name'), 'order_proposals.shop_notes', 'orders.customer_id', 'customers.first_name', 'customers.last_name', 'customers.phone', 'orders.customer_notes', 'orders.city', 'orders.street', 'orders.building', 'orders.entrance', 'orders.apartment', 'orders.latitude', 'orders.longitude', 'orders.status', 'order_proposals.delivery_time', 'order_proposals.price', 'delivery_price', 'order_proposals.proposed_at', 'order_proposals.accepted_at', 'order_proposals.declined_at', 'order_proposals.processed_at', 'orders.created_at', 'orders.updated_at')
            ->whereRaw('orders.customer_id = ' . $customer_id . ' AND orders.status BETWEEN 5 AND 6 AND (orders.proposal_id IS NULL OR orders.proposal_id = order_proposals.id) AND orders.created_at > (DATE_SUB(DATE_FORMAT(CURDATE(), \'%Y-%m-01\'), INTERVAL ' . (($page - 1) * 2 + 1) . ' MONTH)) AND orders.created_at < (DATE_SUB(DATE_FORMAT(CURDATE(), \'%Y-%m-01\'), INTERVAL ' . (($page - 1) * 2 - 1) . ' MONTH))')
            ->orderByRaw('TIME_FORMAT(orders.created_at, "%Y%m") DESC, orders.id DESC');
            //->orderByRaw('TIME_FORMAT(orders.created_at, "%Y%m") DESC, customers.first_name, customers.last_name, order_proposals.price DESC, TIME_FORMAT(orders.created_at, "%Y%m%d") DESC');
    }

}
