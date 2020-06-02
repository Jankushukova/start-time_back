<?php


namespace App\Http\Controllers\Api\V1\User;


use App\Follower;
use App\Helpers\CollectionHelper;
use App\Http\Controllers\Api\V1\Project\ProjectOrderController;
use App\Http\Controllers\Api\V1\Project\ProjectsController;
use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectOrder;
use App\User;
use App\Role;
use Dosarkz\EPayKazCom\Facades\Epay;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
class UsersController extends Controller
{
    public function __construct()
    {

    }

    public function UserProfileInformation(){
        $user = JWTAuth::parseToken()->authenticate();
        $user->image = asset($user->image);
        $user['projectsCount'] = Project::whereOwnerId($user->id)->count();
        $user['bakersCount'] = count(ProjectOrderController::getUserBakers($user->id));
        $user['bakedCount'] = ProjectOrder::whereUserId($user->id)->count();
        $user['followersCount'] = Follower::whereFollowedId($user->id)->count();
        $user['followedCount'] = Follower::whereFollowingId($user->id)->count();
        $user['recommendationCount'] = count($this->buildRecommendations($user));
        return $user;
    }
    public function userRecommendations(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $recommendations = $this->buildRecommendations($user);
        return CollectionHelper::paginate($recommendations, count($recommendations), $request->perPage);
    }

    public function buildRecommendations($user){
        $query = DB::table('projects')
            ->select('projects.id')
            ->join('users', 'users.id', 'projects.owner_id')
            ->join('followers', 'followers.followed_id','users.id')
            ->where('followers.following_id', $user->id)
            ->where('projects.active', 1)
            ->get()->map(function($item, $key){
                return $item->id;
            });
        $recommendations = Project::all()->whereIn('id', $query)->values()->map(function ($item, $key){
            $item->images;
            $item->likes;
            $item->user;
            try {
                $user = JWTAuth::parseToken()->authenticate();
                $item['liked'] = $item->liked($user->id);

            } catch (JWTException $e) {
            }
            return $item;
        });
        return $recommendations;
    }


    public function getPartners(){
        return User::all()->where('partner', '=', 1)->values();
    }
    public function open()
    {
        return "dvavdavdavdavdav";

    }

    public function closed()
    {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'),200);
    }


    public function passwordReset(Request $request){
        return $request->all();
    }




    public function index()
    {
        return User::all();
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findorFail($id);
        $user->baked;

        $user->followers;
        $user->projects;
        return $user;
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
        $course = User::findorFail($id);
        $course->update($request->all());
        return $course;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = User::findOrFail($id);
        $course->delete();
        return response()->json(['success' => true]);
    }





}
