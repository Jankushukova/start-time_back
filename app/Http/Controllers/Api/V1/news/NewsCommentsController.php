<?php

namespace App\Http\Controllers\API\V1\news;

use App\Http\Controllers\Controller;
use App\News;
use App\NewsComment;
use Illuminate\Http\Request;

class NewsCommentsController extends Controller
{
    public function index()
    {
        return NewsComment::all();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCommentsOfNews($id)
    {
        return News::findOrFail($id)->comments;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $comment = NewsComment::create($request->all());
        $comment->save();
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

        return NewsComment::findorFail($id);
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
        $comment = NewsComment::findorFail($id);
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
        $comment = NewsComment::findOrFail($id);
        $comment->delete();
        return response()->json(['success' => true]);
    }
}
