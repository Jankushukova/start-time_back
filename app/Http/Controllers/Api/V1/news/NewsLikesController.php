<?php

namespace App\Http\Controllers\API\V1\News;

use App\Http\Controllers\Controller;
use App\News;
use App\NewsLike;
use App\Project;
use App\ProjectLike;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NewsLikesController extends Controller
{
    public function index()
    {
        return NewsLike::all();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLikesOfNews($id)
    {
        return News::findOrFail($id)->likes;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $news = News::findOrFail($request->news_id);
        if ($news->liked(JWTAuth::parseToken()->authenticate()->id)){
            return response()->json(['error' => true]);

        }else{
            $like = NewsLike::create($request->all());
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

        return NewsLike::findorFail($id);
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
        $like = NewsLike::findorFail($id);
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

        $news = News::findOrFail($id);
        if ($news->liked(JWTAuth::parseToken()->authenticate()->id)){
            $like = NewsLike::where('news_id', $id)
                ->where('user_id',JWTAuth::parseToken()->authenticate()->id)->first();
            $like->delete();
            return $like;

        }
        return response()->json(['error' => true]);
    }
}
