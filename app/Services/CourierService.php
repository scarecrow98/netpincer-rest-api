<?php

namespace App\Services;

use App\Models\Courier;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CourierService {


    public function registerCourier($data) {
        $this->validateRegistrationData($data);

        Courier::insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => Hash::make($data['password'])
        ]);
    }

    private function validateRegistrationData($data) {
        $validator = Validator::make($data, [
            'email'         => 'required|unique:couriers|max:255|email:filter',
            'name'          => 'required|max:255',
            'phone_number'  => 'required|regex:/^06\d{0,9}$/',
            'password'      => 'required|min:8',
            'password_again'=> 'required|min:8|same:password'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }
}