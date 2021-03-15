<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    public $timestamps = false;
    public $table = 'products';
    protected $fillable = [
        'product_category_id',
        'name',
        'description',
        'unit_price',
        'discount'
    ];

    public function category() {
        return $this->belongsTo('App\Models\ProductCategory', 'product_category_id', 'id');
    }
}