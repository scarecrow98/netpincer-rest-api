<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function registrate(Request $req) {
        $validator = Validator::make($req->all(), [
            'email'         => 'required|unique:users|max:255|email:filter',
            'post_code'     => 'required|digits:4',
            'city'          => 'required|max:255',
            'street'        => 'required|max:255',
            'name'          => 'required|max:255',
            'phone_number'  => 'required|regex:/^06\d{0,9}$/',
            'password'      => 'required|min:8',
            'password_again'=> 'required|min:8|same:password'
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }
        
        $data = $req->all();

        $user = new User;
        $user->email = $data['email'];
        $user->address = $data['post_code'] .' '. $data['city'] . ', ' . $data['street'];
        $user->name = $data['name'];
        $user->phone_number = $data['phone_number'];
        $user->password = Hash::make($data['password']);
        $user->saveOrFail();

        return $this->success();
    }
}
