<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model {
    public $timestamps = false;
    public $table = 'partners';
    public $with = [ 'type' ];
    public $hidden = [ 'password', 'login_id', 'open_times', 'product_categories' ];
    public $appends = [ 'category_list', 'time_table' ];

    private $day_map = [
        'mon'   => 'hétfő',
        'tue'   => 'kedd',
        'wed'   => 'szerda',
        'thu'   => 'csütörtök',
        'fri'   => 'péntek',
        'sat'   => 'szombat',
        'sun'   => 'vasárnap'
    ];

    public function type() {
        return $this->belongsTo('App\Models\PartnerType', 'partner_type_id', 'id');
    }

    public function open_times() {
        return $this->hasMany('App\Models\PartnerOpenTime', 'partner_id', 'id');
    }

    public function product_categories() {
        return $this->hasMany('App\Models\ProductCategory', 'partner_id', 'id');
    }

    public function getTimeTableAttribute() {
        return array_map(function($row) {
            return [
                'from'      => $row['open_from'],
                'to'        => $row['open_to'],
                'day_en'    => $row['day'],
                'day_hu'    => $this->day_map[ $row['day'] ]
            ];
        }, $this->open_times->toArray()); 
    }
    
    public function getCategoryListAttribute() {
        return array_map(function($row) {
            return $row['name'];
        }, $this->product_categories->toArray());
    }
}