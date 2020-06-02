<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectCategory;
use App\ProjectLike;
use App\ProjectOrder;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use function foo\func;
use function GuzzleHttp\Promise\all;

class ProjectsController extends Controller
{
    public function __construct()
    {
    }

    public function addView(Request $request){
        $project = Project::findOrFail($request->project_id);
        $project->views = $project->views + 1;
        $project->save();
        return $project;
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


    public function index(Request $request)
    {
        $projects = Project::with('user')->get();
        return CollectionHelper::paginate($projects, count($projects), $request->perPage);

    }



    public function getMostPopular(){
        $ids = [];
        $projects =  Project::all()
        ->whereIn('id', ((Project::select(DB::raw('projects.id, COUNT(project_likes.id) as like_count'))
            ->join('project_likes','project_likes.project_id','=','projects.id')
            ->groupBy('projects.id')
            ->orderBy('like_count', 'desc')
            ->take(6)
            ->get())->map(function ($item, $key) {
            return $item['id'];
        })))->where('active','=','1')->values();

        $projects = $projects->map(function ($item, $key){
            $item->images = $this->setImagePath($item->images);
            $item->likes;
            $item->user;
            return $item;
        });

        return $projects;


    }

    public function setImagePath($images){
        return $images->map(function($item, $key){
            $item->image = asset($item->image);
            return $item;
        });

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


    public function getUserProjects(Request $request){
        $projects = Project::whereOwnerId($request->id)->where('active', 1)->with('likes', 'images', 'bakers')
            ->get()
            ->map(function($item, $key){
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                    $item['liked'] = $item->liked($user->id);

                } catch (JWTException $e) {
                }
                return $item;
            });

        return CollectionHelper::paginate($projects, count($projects), $request->perPage);
    }

    public function getProjectsOfCategory(Request $request)
    {
        $projects = Project::whereCategoryId($request->category_id)
            ->where('active', 1)
            ->with('likes','images','user')
            ->get()
            ->map(function ($item,$key){
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                    $item['liked'] = $item->liked($user->id);

                } catch (JWTException $e) {
                }
                return $item;
            });
        return CollectionHelper::paginate($projects, count($projects), $request->perPage);
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
        $project = Project::whereId($id)->with('images', 'user', 'comments', 'questions', 'likes', 'updates', 'gifts')->first();
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $project['liked'] = $project->liked($user->id);

        } catch (JWTException $e) {
        }
        $project->updates->map(function($item,$key){
            $item->images;
            return $item;
        });
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
