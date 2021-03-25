<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PartnerService {
    public function getProductsForUser($partner_id) {
        $products = Product::where('partner_id', $partner_id)
                            ->orderByDesc('product_category_id')->get();
        

        $products->each(function($product) {
            $product->makeHidden('category');

            if ($product->image) {
                $product->image = url() . '/product_images/' . $product->image;
            } else {
                $product->image = url() . '/images/product_default.jpg';
            }

            $product->unit_price = $product->unit_price . ' Ft';
            $product->discount = $product->discount ? $product->discount . '%' : null;
        });

        return $products;
    }

    public function getPartnersWithinDistance($user_lng, $user_lat, $dist_limit) {
        $partners = DB::select(
            'CALL list_nearby_partners(point(?, ?), ?)',
            [ $user_lng, $user_lat, $dist_limit ]
        );

        foreach ($partners as &$partner) {
            $partner->image = $partner->image ? 
                                        url() . '/partner_images/' . $partner->image : 
                                        url() . '/images/partner_default.png';

            $partner->estimated_delivery_time = 120; //todo később
        }

        return $partners;
    }

    public function registerPartner(GeocodingService $geo_service, $data) {
        $this->validateRegistrationData($data);

        $address_string = $data['post_code'] .' '. $data['city'] . ', ' . $data['street'];
        $geo_data = $geo_service->getCoordinatesFromAddress($address_string);;

        try {
            $geo_data = $geo_service->getCoordinatesFromAddress($address_string);
        } catch(\Exception $ex) {
            throw new \Exception('We could not determine your geolocation from the address you provided');
        }

        DB::insert(
            "INSERT INTO partners(
                partner_type_id,
                name,
                address,
                location,
                description,
                image,
                color_style,
                delivery_fee,
                email,
                password,
                courier_share_percent)
            VALUES(?, ?, ?, point(?,?), ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['partner_type_id'],
                $data['name'],
                $geo_data['formatted_address'],
                $geo_data['lng'], $geo_data['lat'],
                $data['description'],
                $data['image'],
                str_replace('#', '', $data['color_style']),
                $data['delivery_fee'],
                $data['email'],
                Hash::make($data['password']),
                round($data['courier_share_percent'] / 100.0, 1)
            ]
        );

        $partner_id = DB::getPdo()->lastInsertId();

        //nyitvatartási időszak mentése
        foreach ($data['open_times'] as $open_time) {

            DB::table('partner_open_times')->insert([
                'partner_id'    => $partner_id,
                'day'           => $open_time['day'],
                'open_from'     => $open_time['closed']? null : $open_time['from'],
                'open_to'       => $open_time['closed'] ? null : $open_time['to'],
            ]);
        }
    }

    private function validateRegistrationData($data) {
        $validator = Validator::make($data, [
            'partner_type_id' => 'required|integer|exists:partner_types,id',
            'name'          => 'required|max:255',
            'post_code'     => 'required|digits:4',
            'city'          => 'required|max:255',
            'street'        => 'required|max:255',
            'description'   => 'required|max:1000',
            'color_style'   => 'required|max:7',
            'delivery_fee'  => 'required|integer',
            'email'         => 'required|unique:partners|max:255|email:filter',
            'courier_share_percent'         => 'required|numeric|max:100|min:1',
            'password'      => 'required|min:8',
            'password_again'=> 'required|min:8|same:password',
            'open_times'    => 'required'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }
}