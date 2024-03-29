<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectCategory;
use App\ProjectGift;
use App\ProjectImage;
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

    public function filter(Request $request){
        $searchText = $request->searchText;
        $attribute = $request->attribute;
        error_log($searchText);
        error_log($attribute);
        if($attribute==='owner'){
            $projects = Project::with('user')
                ->join('users', 'users.id','=','projects.owner_id')
                ->where('users.firstname', 'like', '%' . $searchText . '%')
                ->orWhere('users.lastname','like', '%' . $searchText . '%' )
                ->get();
        }else{
            $projects = Project::with('user')->where($attribute, 'like', '%' . $searchText . '%')->get();
        }
        return CollectionHelper::paginate($projects, count($projects), $request->perPage);

    }


    public function getAmountOfProjects(){
        return count(Project::all());
    }

    public function getAmountOfSuccessfulProjects(){
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
            ->leftJoin('project_likes','project_likes.project_id','=','projects.id')
            ->groupBy('projects.id')
            ->orderBy('like_count', 'desc')
            ->where('projects.active', 1)
            ->where('project_likes.deleted_at', null)
            ->take(5)
            ->get())->map(function ($item, $key) {
            return $item['id'];
        })))->values();

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

    public function getUserActiveProjects(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $projects = Project::whereOwnerId($user->id)->where('active', Project::ACTIVE_PROJECT)->with('likes', 'images', 'bakers')
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
    public function getUserUnActiveProjects(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $projects = Project::whereOwnerId($user->id)->whereActive(Project::UN_ACTIVE_PROJECT)->orderBy('created_at','desc')->with('likes', 'images', 'bakers')
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
    public function getUserFinishedProjects(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $projects = Project::whereOwnerId($user->id)->whereActive(Project::FINISHED_PROJECT)->orderBy('created_at','desc')->with('likes', 'images', 'bakers')
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
        $project = Project::whereId($id)->with('category','images', 'user', 'comments', 'questions', 'likes', 'updates', 'gifts')->first();
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

    public function showOwnerOfProject($id){
        return Project::findOrFail($id)->user;

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
        $project->images;
        $project->gifts;
        $project->images->map(function ($item, $key){
            ProjectImage::findOrFail($item->id)->delete();
        });
        $project->gifts->map(function ($item, $key){
            ProjectGift::findOrFail($item->id)->delete();
        });
        $project->update($request->all());
        return $project;
    }

    public function changeState(Request $request){
        $project = Project::findOrFail($request->id);
        error_log('status');
        error_log($project->active);
        $project->active = $request->state;
        $project->save();
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
