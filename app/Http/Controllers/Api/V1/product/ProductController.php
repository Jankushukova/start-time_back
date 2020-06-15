<?php

namespace App\Http\Controllers\API\V1\Product;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    public function addView(Request $request){
        $product = Product::findOrFail($request->product_id);
        $product->views = $product->views + 1;
        $product->save();
        return $product;
    }
    public function index(Request $request)
    {
        $products = Product::with('images','likes')->whereActive(1)->get()->map(function($item, $key){
            try {
                $user = JWTAuth::parseToken()->authenticate();
                $item['liked'] = $item->liked($user->id);

            } catch (JWTException $e) {
            }
            return $item;
        });
        return CollectionHelper::paginate($products, count($products), $request->perPage);

    }

    public function getAllProducts(Request $request){
        $products = Product::with('user', 'project')->get()->map(function ($item, $key){
            $item->project->category;
            return $item;
        });
        return CollectionHelper::paginate($products, count($products), $request->perPage);

    }


    public function getMostPopular(){
        $ids = [];
        $products =  Product::all()
            ->whereIn('id', ((Product::select(DB::raw('products.id, COUNT(product_likes.id) as like_count'))
                ->leftJoin('product_likes','product_likes.product_id','=','products.id')
                ->groupBy('products.id')
                ->orderBy('like_count', 'desc')
                ->where('products.active', 1)
                ->take(8)
                ->get())->map(function ($item, $key) {
                return $item['id'];
            })))->values();

        $products = $products->map(function ($item, $key){
            $item->images;
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
        return Product::where('category_id',$id)->whereActive(1);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        error_log($request->project_id);
        $product = Product::create($request->all());
        $product->save();
        return $product;
    }
    public function filter(Request $request)
    {
        $searchText = $request->searchText;
        $attribute = $request->attribute;
        error_log($searchText);
        error_log($attribute);
        if($attribute == 'owner'){
            $products = Product::select(DB::raw('products.*'))
                ->join('users', 'users.id', '=', 'products.owner_id')
                ->where('users.fullname', 'like','%'.$searchText . '%')
                ->get();
        }else {
            $products = Product::where('products.'.$attribute,'like','%'.$searchText.'%')->get();
        }
        $products = $products->map(function ($item, $key){
           $item->user;
           $item->project;
           $item->project->category;
           return $item;
        });

        return CollectionHelper::paginate($products, count($products), $request->perPage);

    }

        /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $product =  Product::findorFail($id);
        $product->images;
        $product->user;
        return $product;
    }

    public function getUserActiveProducts(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $products = Product::whereOwnerId($user->id)->where('active', 1)->with('likes', 'images')
            ->get()
            ->map(function($item, $key){
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                    $item['liked'] = $item->liked($user->id);

                } catch (JWTException $e) {
                }
                return $item;
            });

        return CollectionHelper::paginate($products, count($products), $request->perPage);
    }
    public function getUserUnActiveProducts(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $products = Product::whereOwnerId($user->id)->where('active', 0)->with('likes', 'images')
            ->get()
            ->map(function($item, $key){
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                    $item['liked'] = $item->liked($user->id);

                } catch (JWTException $e) {
                }
                return $item;
            });

        return CollectionHelper::paginate($products, count($products), $request->perPage);
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
        $product->images->map(function ($item, $key){
            ProductImage::findOrFail($item->id)->delete();
        });
        $product->update($request->all());
        return $product;
    }

    public function changeState(Request $request){
        $product = Product::findOrFail($request->product_id);
        $product->active = !$product->active;
        $product->save();
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
