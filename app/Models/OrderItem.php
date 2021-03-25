<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
    public $timestamps = false;
    public $table = 'order_items';

    public $appends = ['subtotal', 'product_name'];

    public function getSubtotalAttribute() {
        return $this->quantity * $this->unit_price;
    }

    public function getProductNameAttribute() {
        return Product::select('name')->where('id', $this->product_id)->first()->name;
    }

}