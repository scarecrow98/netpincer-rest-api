<?php

namespace App\Http\Controllers;

use App\Services\CourierService;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class CourierController extends Controller
{
    public function registrate(Request $req, CourierService $courier_service, GeocodingService $geo_service) {
        try {
            $courier_service->registerCourier($req->all(), $geo_service);
            return $this->success(null, 'Regisztáció sikeres');
        } catch(Exception $e) {
            return $this->fail(null, $e->getMessage());
        }

    }
}
