<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Partner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    public function list(Request $req) {
        $user_lng = $req->input('user_lng') ?? 18.9210095;
        $user_lat = $req->input('user_lat') ?? 46.8033416;
        $dist_limit = $req->input('dist_limit') ?? 3000;

        $partners = DB::select(
            'CALL list_nearby_partners(point(?, ?), ?)',
            [ $user_lng, $user_lat, $dist_limit ]
        );

        foreach ($partners as &$partner) {
            $partner->image = url() . '/partner_images/etterem.jpg';
            $partner->current_open_time = '08:00 - 16:00';
            $partner->estimated_delivery_time = 120;
        }

        return $partners;
    }

    public function profile($id) {
        try {
            return $this->success(Partner::findOrFail($id));
        } catch(ModelNotFoundException $e) {
            return $this->fail(null, 'No partner found with the given id');
        }
    }

    public function categoryList($id) {
        try {
            return $this->success(Partner::findOrFail($id)->product_categories);
        } catch(ModelNotFoundException $e) {
            return $this->fail(null, 'No partner found with the given id');
        }
    }

    public function partnerTypeList() {
        return DB::table('partner_types')->get();
    }

    public function registrate(Request $req, GeocodingService $geo_service) {
        $validator = Validator::make($req->all(), [
            'partner_type_id' => 'required|integer|exists:partner_types,id',
            'name'          => 'required|max:255',
            'post_code'     => 'required|digits:4',
            'city'          => 'required|max:255',
            'street'        => 'required|max:255',
            'description'   => 'required|max:1000',
            'color_style'   => 'required|max:7',
            'delivery_fee'  => 'required|integer',
            'email'         => 'required|unique:partners|max:255|email:filter',
            'courier_share_percent'         => 'required|numeric|max:1.0',
            'password'      => 'required|min:8',
            'password_again'=> 'required|min:8|same:password'
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
            return $this->fail('We could not determine your geolocation from the address you provided');
        }

        $success = DB::insert(
            "INSERT INTO partners(
                partner_type_id,name,
                address,location,description,
                image,color_style,delivery_fee,
                email,password,courier_share_percent)
            VALUES(?,?,?,point(?,?),?,?,?,?,?,?,?)",
            [
                $data['partner_type_id'],
                $data['name'],
                $geo_data['formatted_address'],
                $geo_data['lng'], $geo_data['lat'],
                $data['description'],
                'cover.png',
                str_replace('#', '', $data['color_style']),
                $data['delivery_fee'],
                $data['email'],
                Hash::make($data['password']),
                $data['courier_share_percent']
            ]
        );

        if ($success) {
            return $this->success();
        }

        return $this->fail();
    }

    public function geotest(Request $req, GeocodingService $geo_service) {
        $address = $req->input('address');
        try {
            return $geo_service->getCoordinatesFromAddress($address);
        } catch(\Exception $ex) {
            return [ 'status' => $ex->getMessage() ];
        }
    }
}
