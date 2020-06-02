<?php

namespace App\Http\Controllers\API\V1\News;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\News;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class NewsController extends Controller
{

    public function addView(Request $request){
        $news = News::findOrFail($request->news_id);
        $news->views = $news->views + 1;
        $news->save();
        return $news;
    }

    public function index(Request $request)
    {
        $news =  News::with('likes', 'images')->get()->map(function($item, $key){
            try {
                $user = JWTAuth::parseToken()->authenticate();
                $item['liked'] = $item->liked($user->id);
            } catch (JWTException $e) {
            }
            return $item;
        });

        return CollectionHelper::paginate($news, count($news), $request->perPage);

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImagesOfNews($id)
    {
        return News::where('project_id',$id);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $image = News::create($request->all());
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

        $news = News::findorFail($id);
        $news->images;
        $news->comments->map(function($item, $key){
            $item->user;
            return $item;
        });
        return $news;
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
        $image = News::findorFail($id);
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
        $image = News::findOrFail($id);
        $image->delete();
        return response()->json(['success' => true]);
    }
}
