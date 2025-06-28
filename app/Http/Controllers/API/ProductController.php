<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $products = Product::query()->get();
        // return ProductResource::collection($products);
        return $this->jsonData(true, "Products fetched", ProductResource::collection($products));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            "name" => ["required"],
            "expiry_date" => ["required", "date"],
        ]);

        if ($validator->fails()) {
            return $this->jsonData(false, "Validation error", $validator->validated(), $validator->errors(), 400);
        }
        $img = $request->file("image");
        $url = null;
        if($img){
            $url = Storage::put("images", $img);
        }
        $product = Product::create($data);
        $product->image = $url;
        $product->save();
        return $this->jsonData(true, "Product created", new ProductResource($product->refresh()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $this->jsonData(true, "Product details fetched", new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            "name" => ["required"],
            "expiry_date" => ["required", "date"],
        ]);

        if ($validator->fails()) {
            return $this->jsonData(false, "Validation error", $validator->validated(), $validator->errors(), 400);
        }
        $img = $request->file("image");
        $url = $product->image;
        if($img){
            Storage::delete($product->image);
            $url = Storage::put("images", $img);
        }
        $data["image"] = $url;
        $product->update($request->all());
        $product->image = $url;
        $product->save();
        return $this->jsonData(true, "Product updated", new ProductResource($product));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        $url = $product->image;
        if ($url) {
            Storage::delete($product->image);
        }
        return $this->jsonData(true, "Product deleted");
    }

    /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($search)
    {
        $products = Product::where('name', 'like', '%'.$search.'%')
        ->orWhere('category', 'like', '%' . $search . '%')
        ->get();
        return $this->jsonData(true, "Result fecthed", ProductResource::collection($products));
    }

    public function allProducts(Request $request, $id = null){
        $products = Product::withoutGlobalScope("for-user")->with("user");
        if($id){
            $products = $products->where("id", $id)->firstOrFail();
            return $this->jsonData(true,"Product fetched", $products);
        }
        $products = $products->orderBy("created_at", "DESC")->paginate($request->get("per_page", 100));
        return $this->jsonData(true,"Products fetched", $products);
    }
}
