<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Partner;
use App\Services\PartnerService;
use App\Services\UploadService;

class PartnerController extends Controller
{
    public function list(Request $req, PartnerService $partner_service) {
        $user_lng = $req->input('user_lng') ?? 18.9210095;
        $user_lat = $req->input('user_lat') ?? 46.8033416;
        $dist_limit = $req->input('dist_limit') ?? 3000;

        return $partner_service->getPartnersWithinDistance($user_lng, $user_lat, $dist_limit);
    }

    public function productListForUser(PartnerService $partner_service, $partner_id) {
        try {
            return $partner_service->getProductsForUser($partner_id);
        } catch(\Exception $ex) {
            return $this->fail(null, $ex->getMessage());
        }
    }

    public function profile($id) {
        return Partner::findOrFail($id);
    }

    public function categoryList($id) {
        return Partner::findOrFail($id)->product_categories;
    }

    public function partnerTypeList() {
        return DB::table('partner_types')->get();
    }

    public function registrate(Request $req, GeocodingService $geo_service, PartnerService $partner_service, UploadService $upload_service) {

        try {
            DB::beginTransaction();

            $image_name = $upload_service->uploadPartnerImage($req->file('partnerImage'));

            $partner_data = json_decode($req->input('partnerData') ?? '{}', true);
            $partner_data['image'] = $image_name;
            $partner_service->registerPartner($geo_service, $partner_data);
        
            DB::commit();

            return $this->success();
        } catch(\Exception $ex) {
            DB::rollBack();
            return $this->fail(null, $ex->getMessage());
        }
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
