<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function success($data = null, $message = '') {
        return response()->json([
            'status'    => true,
            'message'   => $message,
            'data'      => $data
        ]);
    }

    public function fail($data = null, $message = '') {
        return response()->json([
            'status'    => false,
            'message'   => $message,
            'data'      => $data
        ]);
    }
}
