<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function all_product(){
        $products = Product::orderBy('id','DESC')->get();
        return response()->json([
            'products' => $products
        ],200);
    }
}
