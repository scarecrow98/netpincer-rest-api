<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function list(Request $req) {
        return Partner::select(['name', 'image', 'delivery_fee'])->get();
    }
}
