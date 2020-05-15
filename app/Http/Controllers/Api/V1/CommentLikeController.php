<?php

namespace App\Http\Controllers\API\V1;

use App\CommentLike;
use App\Http\Controllers\Controller;
use App\NewsComment;
use App\ProjectComment;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentLikeController extends Controller
{
    public function index()
    {
        return CommentLike::all();
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLikesOfComment($id)
    {
        return ProjectComment::findOrFail($id)->likes;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($comment = ProjectComment::find($request->project_comment_id)){
            if ($comment->liked(JWTAuth::parseToken()->authenticate()->id)){
                return response()->json(['error' => true]);

            }else{
                $like = CommentLike::create($request->all());
                $like->save();
            }
        }if($comment = NewsComment::find($request->news_comment_id)){
            if ($comment->liked(JWTAuth::parseToken()->authenticate()->id)){
                return response()->json(['error' => true]);

            }else{
                $like = CommentLike::create($request->all());
                $like->save();
            }
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

        return CommentLike::findorFail($id);
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
        $like = CommentLike::findorFail($id);
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
        if($comment = ProjectComment::find($id)){
            if ($comment->liked(JWTAuth::parseToken()->authenticate()->id)){
                $like = CommentLike::where('project_comment_id', $id)
            ->where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
                $like->delete();
                return $like;

            }
        }
        if($comment = NewsComment::find($id)){
            if ($comment->liked(JWTAuth::parseToken()->authenticate()->id)){
                $like = CommentLike::where('news_comment_id', $id)
                    ->where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
                $like->delete();
                return $like;

            }
        }

        return response()->json(['error' => true]);
    }
}
