<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;


class Partner extends Model implements AuthenticatableContract, JWTSubject {
    use Authenticatable;
    
    public $timestamps = false;
    public $table = 'partners';
    public $hidden = [ 'password', 'open_times', 'product_categories', 'location' ];
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

    public function getDistanceFromUserAttribute() {
        return 1340;
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}