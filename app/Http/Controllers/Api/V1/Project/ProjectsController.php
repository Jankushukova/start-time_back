<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectCategory;
use App\ProjectLike;
use App\ProjectOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    public function __construct()
    {
    }

    public function getAmountOfProjects(){
        return count(Project::all());
    }

    public function getAmountOfSuccessfulProjects(){
//        $project = Project::findOrFail(5);
//        echo $project->gathered<$project->goal;
        return count(Project::whereColumn('gathered', '>=', 'goal')->get());
    }

    public function getAmountOfBakers(){
        return count(ProjectOrder::select('user_id')->distinct()->get());
    }


    public function index()
    {
        return Project::all();
    }

    public function getMostPopular(){
        $ids = [];
        $projects =  Project::all()
        ->whereIn('id', ((Project::select(DB::raw('projects.id, COUNT(project_likes.id) as like_count'))
            ->leftJoin('project_likes','project_likes.project_id','=','projects.id')
            ->groupBy('projects.id')
            ->orderBy('like_count', 'desc')
            ->take(5)
            ->get())->map(function ($item, $key) {
            return $item['id'];
        })))->where('active','=','1')->values();

        $projects = $projects->map(function ($item, $key){
            $item->images;
            $item->user;
            return $item;
        });

        return $projects;


    }




    public function getImagesOfProject($id){
        return Project::findOrFail($id)->images;
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


    public function getProjectProjects($id){
        return (Project::findorFail($id))->projects;
    }

    public function getProjectsOfCategory($id)
    {
        $projects = ProjectCategory::findOrFail($id)->projects->where('active','=','1');
        $projects = $projects->map(function ($item, $key){
            $item->images;
            $item->user;
            return $item;
        });
        return $projects;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $project = Project::create($request->all());


        $project->save();


        return $project ;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = Project::findorFail($id);
        $project->images;
        $project->user;
        $project->comments;
        $project->updates;
        $project->questions;
        $project->likes;
        $backersCount = Project::select(DB::raw('projects.id'))
            ->join('project_orders', 'project_orders.project_id','=','projects.id')
            ->where('projects.id','=',$project->id)->count();
        $project['backers'] = $backersCount;
        return $project;
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
        $project = Project::findorFail($id);
        $project->update($request->all());
        return $project;
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
        $project->delete();
        return response()->json(['success' => true]);
    }
}
