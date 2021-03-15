<?php

namespace App\Services;

use Exception;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductService {

    public function saveCategory($category_name) {
        
        if (!$category_name) {
            throw new Exception('Category name cannot be empty!');
        }
        
        $partner_id = auth()->guard('partner')->user()->id;
        if (ProductCategory::where('name', $category_name)->where('partner_id', $partner_id)->exists()) {
            throw new Exception('Category with the same name already exists!');
        }

        $category = new ProductCategory();
        $category->name = $category_name;
        $category->partner_id = $partner_id;
        $category->saveOrFail();
    }

    public function saveProduct($product_data) {
        $validator = $this->validateProductData($product_data);
        
        $partner_id = auth()->guard('partner')->user()->id;

        if (!ProductCategory::where('id', $product_data['product_category_id'])->where('partner_id', $partner_id)->exists()) {
            throw new Exception('The given category id does not exists for the actual partner');
        }


        if (in_array($product_data['id'], [-1, null])) {
            $model = new Product();
        } else {
            $model = Product::findOrFail($product_data['id']);
        }
        $model->fill($validator->validated());
        $model->partner_id = $partner_id;


        //ha volt meglévő képe a terméknek, de újat töltöttek fel hozzá
        if ($product_data['image']) {
            if ($model->image && file_exists('./product_images/' . $model->image)) {
                unlink('./product_images/' . $model->image);
            }
            $model->image = $product_data['image'];
        }

        $model->saveOrFail();

        $this->saveProductAllergens($model->id, $product_data);
    }

    private function saveProductAllergens($product_id, $product_data) {
        if (!is_array($product_data['product_allergen_ids'])) {
            return;
        }

        $saved_allergen_ids = DB::table('product_has_allergen')
                                ->where('product_id', $product_id)
                                ->pluck('product_allergen_id')->toArray();
        $allergen_ids = $product_data['product_allergen_ids'];


        $ids_to_delete = array_diff($saved_allergen_ids, $allergen_ids);
        foreach ($ids_to_delete as $allergen_id) {
            DB::table('product_has_allergen')
                    ->where('product_allergen_id', $allergen_id)
                    ->where('product_id', $product_id)->delete();
        }

        foreach ($allergen_ids as $allergen_id) {
            if (!DB::table('product_has_allergen')
                ->where('product_id', $product_id)
                ->where('product_allergen_id', $allergen_id)->exists()) {

                DB::table('product_has_allergen')->insert([
                    'product_id' => $product_id,
                    'product_allergen_id' => $allergen_id
                ]);
            }
        }
    }
    
    private function validateProductData($data) {
        $validator = Validator::make($data, [
            'product_category_id' => 'required|integer',
            'name' => 'required|max:255',
            'description' => 'required|max:1000',
            'unit_price' => 'required|numeric',
            'discount' => 'nullable|numeric|max:100.0|min:0.0',
            'productImage' => 'image'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        return $validator;
    }
}