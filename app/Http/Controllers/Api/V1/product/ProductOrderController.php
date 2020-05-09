<?php

namespace App\Http\Controllers\API\V1\product;

use App\Http\Controllers\Controller;
use App\Product;
use App\ProductOrder;
use Illuminate\Http\Request;

class ProductOrderController extends Controller
{
    public function index()
    {
        return NewsImage::all();
    }

    public function getOrdersOfProduct($id){
        return Product::findOrFail($id)->ordered;
    }

    public function getPaymentsOfProduct($id){
        return Product::findOrFail($id)->payments;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImagesOfNews($id)
    {
        return NewsImage::where('project_id',$id);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $image = NewsImage::create($request->all());
        $image->save();
        return $image;
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
