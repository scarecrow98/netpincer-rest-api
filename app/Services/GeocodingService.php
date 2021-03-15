<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GeocodingService {
    private $api_key;
    private $api_url;

    public function __construct() {
        $this->api_key = env('GOOGLE_API_KEY');
        $this->api_url = env('GEOCODING_API_URL');
    }

    public function getCoordinatesFromAddress(string $address) {
        $response = Http::get($this->api_url, [
            'address'   => $address,
            'key'       => $this->api_key
        ]);

        $response->throw();


        $data = $response->json();

        if ($data['status'] != 'OK') {
            throw new Exception();
        }

        return [
            'lat' => $data['results'][0]['geometry']['location']['lat'],
            'lng' => $data['results'][0]['geometry']['location']['lng'],
            'formatted_address' => $data['results'][0]['formatted_address']
        ];
    }
}