<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\GeocodingService;


class UserController extends Controller
{
    public function registrate(Request $req, GeocodingService $geo_service) {
        $validator = Validator::make($req->all(), [
            'email'         => 'required|unique:users|max:255|email:filter',
            'post_code'     => 'required|digits:4',
            'city'          => 'required|max:255',
            'street'        => 'required|max:255',
            'name'          => 'required|max:255',
            'phone_number'  => 'required|regex:/^06\d{0,9}$/',
            'password'      => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }
        
        $data = $req->all();
        $address_string = $data['post_code'] .' '. $data['city'] . ', ' . $data['street'];
        $geo_data = null;

        try {
            $geo_data = $geo_service->getCoordinatesFromAddress($address_string);
        } catch(\Exception $ex) {
            return $this->fail(['We could not determine your geolocation from the address you provided']);
        }

        $success = DB::insert(
            "INSERT INTO users(
                email,
                address,
                location,
                name,
                phone_number,
                password
            ) VALUES(?, ?, point(?, ?), ?, ?, ?)",
            [
                $data['email'],
                $geo_data['formatted_address'],
                $geo_data['lng'], $geo_data['lat'],
                $data['name'],
                $data['phone_number'],
                Hash::make($data['password'])
            ]
        );

        if ($success) {
            return $this->success();
        }

        return $this->fail();
    }
}
