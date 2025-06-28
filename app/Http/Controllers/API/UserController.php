<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(Request $request){
        $data = User::query()->withCount("products")->paginate($request->get("per_page", 50));
        return $this->jsonData(true, "Users fetched", $data);
    }

    public function view(Request $request, $id){
        $data = User::find($id);
        return $this->jsonData(true,"User fetched", $data);
    }

    public function getUserProducts(Request $request, $id){
        $user = User::findOrFail($id);
        $products = Product::withoutGlobalScope("for-user")->where("user_id", $user->id)->paginate($request->get("per_page", 100));
        return $this->jsonData(true,"User product fetched",[
            "user" => $user,
            "products" => $products
        ]);
    }
}
