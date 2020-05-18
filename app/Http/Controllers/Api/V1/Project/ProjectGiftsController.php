<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectGift;
use Illuminate\Http\Request;

class ProjectGiftsController extends Controller
{
    public function index()
    {
        return ProjectGift::all();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGiftsOfProject($id)
    {
        return Project::findOrFail($id)->gifts;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach ($request->all() as $gift){
            $gift = ProjectGift::create($gift);
            $gift->save();
         }

        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return ProjectGift::findorFail($id);
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
        $gift = ProjectGift::findorFail($id);
        $gift->update($request->all());
        return $gift;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gift = ProjectGift::findOrFail($id);
        $gift->delete();
        return response()->json(['success' => true]);
    }}
