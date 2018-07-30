<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    
    // Variables

    protected $fillable = [
        'email', 'password', 'type'
    ];

    protected $hidden = [
        'password', 'remember_token', 'deleted_at'
    ];

    // Functions

    public function customer()
    {
        return $this->hasOne('App\Customer');
    }

    public function shop()
    {
        return $this->hasOne('App\Shop')->withTrashed();
    }

    public function orders()
    {
        return $this->hasMany('App\Order', 'customer_id');
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification');
    }

}
