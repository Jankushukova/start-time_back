<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\CommentLike;
use App\Project;
use App\ProjectComment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectCommentsController extends Controller
{
    public function index()
    {
        return ProjectComment::all();
    }

    public function getCommentsOfProject($id)
    {
        return Project::findOrFail($id)->comments->map(function($item,$key){
            $item['likes'] = ProjectComment::findOrFail($item->id)->likes->map(function ($item,$key){
                return $item['id'];
            });
            $item->user;
            try {
                $user = JWTAuth::parseToken()->authenticate();
                $item['liked'] = $item->liked($user->id);

            } catch (JWTException $e) {
            }
            return $item;
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $comment = ProjectComment::create($request->all());
        $comment->save();
        $comment->user;
        $comment->likes;
        return $comment;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return ProjectComment::findorFail($id);
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
        $comment = ProjectComment::findorFail($id);
        $comment->update($request->all());
        return $comment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = ProjectComment::findOrFail($id);
        $comment->delete();
        return response()->json(['success' => true]);
    }
}
