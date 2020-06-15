<?php

namespace App\Http\Controllers\API\V1\Product;

use App\Http\Controllers\Controller;
use App\Product;
use App\ProductImage;
use App\Project;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function index()
    {
        return ProductImage::all();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImages($id){
        return Product::findOrFail($id)->images;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $i = 1;
        while ($request->has('image'.$i))
        {
            $image = new ProductImage();
            if($request->hasFile('image'.$i)){
                $file      = $request->file('image'.$i);
                $filename  = $file->getClientOriginalName();
                $picture   = date('His').'-'.$filename;
                $file->move(public_path(ProductImage::PATH), $picture);
                $image->image = ProductImage::PATH.'/'.$picture;
            }
            else if( $request->has('image'.$i)){
                $image->image = str_replace(asset('/'), '' ,$request->get('image'.$i));

            }
            $i++;
            if($request->has('product_id')){
                $image->product_id = $request->get('product_id');
                $image->save();
            }
            else{
                return response()->json(["error" => "Select image first."]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return NewsImage::findorFail($id);
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
        $image = NewsImage::findorFail($id);
        $image->update($request->all());
        return $image;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = NewsImage::findOrFail($id);
        $image->delete();
        return response()->json(['success' => true]);
    }
}
