<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{

    public function allergenList() {
        return DB::table('product_allergens')->get();
    }
}
