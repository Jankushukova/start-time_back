<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\ProductImage;
use App\Project;
use App\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectImagesController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return ProjectImage::all();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImagesOfProject($id)
    {
        return Project::findOrFail($id)->images;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        error_log('store');
         $i = 1;
        while ($request->has('image'.$i))
        {
            $image = new ProjectImage();
            if($request->hasFile('image'.$i)){
                $file      = $request->file('image'.$i);
                $filename  = $file->getClientOriginalName();
                $picture   = date('His').'-'.$filename;
                $file->move(public_path(ProjectImage::PATH), $picture);
                $image->image = ProjectImage::PATH.'/'.$picture;
            }
            else if( $request->has('image'.$i)){
                $image->image = str_replace(asset('/'), '' ,$request->get('image'.$i));

            }
            $i++;
            if($request->has('project_id')){
                $image->project_id = $request->get('project_id');
                $image->save();
            }
            else{
                return response()->json(["error" => "Select image first."]);
            }
        }
    }
//    public function storeContentImage(Request $request){
//        if($request->hasFile())
//    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return ProjectImage::findorFail($id);
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
        $image = ProjectImage::findorFail($id);
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
        $image = ProjectImage::findOrFail($id);
        $image->delete();
        return response()->json(['success' => true]);
    }
}
