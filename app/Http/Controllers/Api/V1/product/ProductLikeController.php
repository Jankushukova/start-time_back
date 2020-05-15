<?php

namespace App\Http\Controllers\Api\V1\product;

use App\Http\Controllers\Controller;
use App\NewsLike;
use App\Product;
use App\ProductLike;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductLikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        if ($product->liked(JWTAuth::parseToken()->authenticate()->id)){
            return response()->json(['error' => true]);

        }else{
            $like = ProductLike::create($request->all());
            $like->save();
        }
        return $like;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductLike  $productLike
     * @return \Illuminate\Http\Response
     */
    public function show(ProductLike $productLike)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductLike  $productLike
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductLike $productLike)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductLike  $productLike
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductLike $productLike)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductLike  $productLike
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $product = Product::findOrFail($id);
        if ($product->liked(JWTAuth::parseToken()->authenticate()->id)){
            $like = ProductLike::where('product_id', $id)
                ->where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
            $like->delete();
            return $like;

        }
        return response()->json(['error' => true]);
    }
}
