<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ProductService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {

    public function get($id) {
        $product = Product::findOrFail($id);

        //allergének
        $product->product_allergen_ids = DB::table('product_has_allergen')
                                            ->where('product_id', $product->id)
                                            ->pluck('product_allergen_id')->toArray();
        return $product;
    }

    public function list() {
        $partner_id = auth()->guard('partner')->user()->id;

        $query = Product::with(['category'])->where('partner_id', $partner_id);

        if (!empty($_GET['filterText'])) {
            $query->where('name', 'like', '%' . $_GET['filterText'] . '%');
        }

        if (!empty($_GET['categoryId'])) {
            $query->where('product_category_id', $_GET['categoryId']);
        }

        $products = $query->get();
        $products->each(function($product) {
            if ($product->image) {
                $product->image = url() . '/product_images/' . $product->image;
            } else {
                $product->image = url() . '/images/product_default.jpg';
            }
        });

        return $products;
    }

    public function saveCategory(Request $req, ProductService $product_service) {
        try {
            $product_service->saveCategory($req->input('categoryName'));
            return $this->success();
        } catch(\Exception $ex) {
            return $this->fail(null, $ex->getMessage());
        }
    }

    public function saveProduct(Request $req, ProductService $product_service, UploadService $upload_service) {
        try {
            DB::beginTransaction();

            $image_name = $upload_service->uploadProductImage($req->file('productImage'));

            $product_data = json_decode($req->input('product') ?? '{}', true);
            $product_data['image'] = $image_name;
            $product_service->saveProduct($product_data);

            DB::commit();
            return $this->success();
        } catch(\Exception $ex) {
            DB::rollBack();
            return $this->fail(null, $ex->getMessage());
        }
    }

    public function getCategories() {
        $partner_id = auth()->guard('partner')->user()->id;
        return ProductCategory::select([ 'id', 'name' ])->where('partner_id',  $partner_id)->get();
    }
}
