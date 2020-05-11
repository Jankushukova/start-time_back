<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectLike;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectLikesController extends Controller
{
    public function index()
    {
        return ProjectLike::all();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLikesOfProject($id)
    {
        return ProjectLike::findOrFail($id)->likes;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $project = Project::findOrFail($request->project_id);
        if ($project->liked(JWTAuth::parseToken()->authenticate()->id)){
            return response()->json(['error' => true]);

        }else{
            $like = ProjectLike::create($request->all());
            $like->save();
        }
        return $like;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return ProjectLike::findorFail($id);
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
        $like = ProjectLike::findorFail($id);
        $like->update($request->all());
        return $like;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        if ($project->liked(JWTAuth::parseToken()->authenticate()->id)){
            $like = ProjectLike::where('project_id', $id)
                ->where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
            $like->delete();
            return $like;

        }
        return response()->json(['error' => true]);
    }
}
