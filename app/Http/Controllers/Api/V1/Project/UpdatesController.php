<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Project;
use App\Update;
use App\UpdateImage;
use Illuminate\Http\Request;

class UpdatesController extends Controller
{
    public function __construct()
    {
    }

    public function getUpdatesOfProject($id){
        return Update::whereProjectId($id)->with('images')->get();
    }

    public function getUpdatesImages($id){
        return Update::findOrFail($id)->images;
    }
    public function index()
    {
        return Update::all();
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

    public function getCurrent()
    {
        return Auth::user();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $update = Update::create($request->all());
        $update->save();
        return $update;
    }

    public function storeImage(Request $request){
        $images = [];
        error_log('store');
        $i = 1;
        error_log($request->has('image'.$i));
        while ($request->has('image'.$i))
        {
            $image = new UpdateImage();
            if($request->hasFile('image'.$i)){
                $file      = $request->file('image'.$i);
                error_log($file);
                $filename  = $file->getClientOriginalName();
                error_log($filename);
                $picture   = date('His').'-'.$filename;
                $file->move(public_path(UpdateImage::PATH), $picture);
                $image->image = UpdateImage::PATH.'/'.$picture;
                error_log($image->image);
            }
            else if( $request->has('image'.$i)){
                $image->image = $request->get('image'.$i);
            }
            $i++;
            if($request->has('update_id')){
                $image->update_id = $request->get('update_id');
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $update = Update::findorFail($id);
        $update->images;
        return $update;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update = Update::findorFail($id);
        $update->update($request->all());
        return $update;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $update = Update::findOrFail($id);
        $update->delete();
        return response()->json(['success' => true]);
    }
}
