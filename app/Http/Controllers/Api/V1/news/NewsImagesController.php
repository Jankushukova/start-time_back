<?php

namespace App\Http\Controllers\API\V1\News;

use App\Http\Controllers\Controller;
use App\NewsImage;
use Illuminate\Http\Request;

class NewsImagesController extends Controller
{
    public function index()
    {
        return NewsImage::all();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImagesOfNews($id)
    {
        return NewsImage::findOrFail($id)->images;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $images = [];
        error_log('store');
        $i = 1;
        error_log($request->has('image'.$i));
        while ($request->has('image'.$i))
        {
            $image = new NewsImage();
            if($request->file('image'.$i)){
                error_log('new img');
                $file = $request->file('image'.$i);
                error_log($file);
                $filename  = $file->getClientOriginalName();
                error_log($filename);
                $picture   = date('His').'-'.$filename;
                $file->move(public_path(NewsImage::PATH), $picture);
                $image->image = NewsImage::PATH.'/'.$picture;
                error_log($image->image);
            }
            else{
                error_log('old img');
                $image->image = str_replace(asset('/'), '' , $request->get('image'.$i));
            }
            $i++;
            if($request->has('news_id')){
                $image->news_id = $request->get('news_id');
                $image->save();
                array_push($images, $image);
            }
            else{
                return response()->json(["error" => "Select image first."]);
            }
        }
        return $images;
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
