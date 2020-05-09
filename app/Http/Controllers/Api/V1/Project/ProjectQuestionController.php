<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectQuestion;
use Illuminate\Http\Request;

class ProjectQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getQuestionsOfProject($id)
    {
        return Project::findOrFail($id)->questions->map(function ($item, $key){
            $item->user;
            return $item;
        });
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $question = ProjectQuestion::create($request->all());
        $question->save();
        $question->user;
        return $question;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProjectQuestion  $projectQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectQuestion $projectQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProjectQuestion  $projectQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(ProjectQuestion $projectQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProjectQuestion  $projectQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProjectQuestion $projectQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProjectQuestion  $projectQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question = ProjectQuestion::findOrFail($id);
        $question->delete();
        return response()->json(['success' => true]);
    }
}
