<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function save(Request $req, OrderService $order_service) {
        try {
            DB::beginTransaction();

            $order_service->saveOrder($req->all());

            DB::commit();

            return $this->success();
        } catch(Exception $ex) {
            DB::rollBack();
            return $this->fail(null, $ex->getMessage());
        }
    }

    public function getLatestOrders(OrderService $order_service) {
        $partner_id = auth()->guard('partner')->user()->id;
        
        return $order_service->getPartnerLatestOrders($partner_id);
    }
}
