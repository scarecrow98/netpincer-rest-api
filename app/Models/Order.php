<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    public $timestamps = false;
    public $table = 'orders';

    public $appends = [ 'total' ];

    public function items() {
        return $this->hasMany('App\Models\OrderItem', 'order_id', 'id');
    }

    public function getTotalAttribute() {
        return $this->items->sum('subtotal');
    }
}