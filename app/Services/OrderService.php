<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Partner;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderService {

    public function __construct(GeocodingService $geo_service) {
        $this->geo_service = $geo_service;
    }

    public function getPartnerLatestOrders($partner_id) {
        return Order::with(['items'])
            ->whereNotIn('status', ['delivered', 'delivering'])
            ->orderByDesc('order_date')
            ->get();
    }

    public function saveOrder($data) {
        $this->validateOrderData($data);

        $order_id = $this->createOrder($data);
        $this->createOrderItems($data['items'], $order_id);
    }

    private function createOrder($data) {
        //$geo_data = $this->geo_service->getCoordinatesFromAddress($data['user_address']);
        $user = auth()->guard('user')->user();

        $courier_share_percent = Partner::select('courier_share_percent')
                                ->where('id', $data['partner_id'])
                                ->first()->courier_share_percent;

        $saved = DB::insert(
            "INSERT INTO orders(partner_id, user_id, payment_type,
            description, needs_delivery, courier_share_percent,
            user_name, user_email, user_phone_number, user_address)
            VALUES(?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?, ?)",
            [
                $data['partner_id'],
                $user ? $user->id : null,
                $data['payment_type'],
                $data['description'],
                $data['needs_delivery'],
                $courier_share_percent,
                $data['user_name'],
                $data['user_email'],
                $data['user_phone_number'],
                $data['user_address']
            ]
        );

        if (!$saved) {
            throw new Exception('Error while saving order');
        }

        //return the order id
        return DB::getPdo()->lastInsertId();
    }

    private function createOrderItems($items, $order_id) {
        foreach ($items as $item) {
            $product = Product::select('unit_price', 'discount')
                                    ->where('id', $item['product_id'])
                                    ->first()->unit_price;

            if ($product->discount) {
                $unit_price = $product->discount * (1.0 - $product->discount/100.0);
            } else {
                $unit_price = $product->unit_price;
            }

            $saved = DB::table('order_items')->insert([
                'order_id'      => $order_id,
                'product_id'    => $item['product_id'],
                'quantity'      => $item['quantity'],
                'unit_price'    => $unit_price
            ]);

            if (!$saved) {
                throw new Exception('Error while saving order items!');
            }
        }
    }

    private function validateOrderData($data) {
        $validator = Validator::make($data, [
            'partner_id' => 'required|exists:partners,id',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email:filter',
            'user_address' => 'required|string|max:255',
            'payment_type' => 'required|in:cash,card',
            'needs_delivery' => 'required|boolean',
            'description' => 'max:255|string|nullable',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }
}