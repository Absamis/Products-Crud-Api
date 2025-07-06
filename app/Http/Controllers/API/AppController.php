<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AppController extends Controller
{
    //
    public function dashboardData(){
        $prodCount = Product::count();
        $latestProd = Product::query()->latest()->limit(10)->orderBy("created_at", "DESC")->get();
        return $this->jsonData(true, "Dashboard fetched", [
            "productCount" => $prodCount,
            "latestProducts" => $latestProd
        ]);
    }
}
