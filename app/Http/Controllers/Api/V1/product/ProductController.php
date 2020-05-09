<?php

namespace App\Http\Controllers\API\V1\product;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function getMostPopular(){
        $ids = [];
        $products =  Product::all()
            ->whereIn('id', ((Product::select(DB::raw('products.id, COUNT(product_likes.id) as like_count'))
                ->leftJoin('product_likes','product_likes.product_id','=','products.id')
                ->groupBy('products.id')
                ->orderBy('like_count', 'desc')
                ->take(8)
                ->get())->map(function ($item, $key) {
                return $item['id'];
            })))->values();

        $products = $products->map(function ($item, $key){
            $item['images'] = Product::findOrFail($item->id)->images;
            return $item;
        });

        return $products;


    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductsOfCategory($id)
    {
        return Product::where('category_id',$id);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::create($request->all());
        $product->save();
        return $product;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return Product::findorFail($id);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findorFail($id);
        $product->update($request->all());
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['success' => true]);
    }
}
