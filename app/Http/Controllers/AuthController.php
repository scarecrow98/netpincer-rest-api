<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function loginUser(Request $req) {
        return $this->login('user', $req->only(['email', 'password']));
    }

    public function loginPartner(Request $req) {
        return $this->login('partner', $req->only(['email', 'password']));
    }

    public function authorizePartner() {
        return [
            'status' =>  auth()->guard('partner')->user() != null
        ];
    }

    private function login(string $guard, array $credentials) {

        if ($token = Auth::guard($guard)->attempt($credentials)) {
            return $this->success([
                'token' => $token
            ]);
        }

        return $this->fail(null, 'Login failed');
    }

}
