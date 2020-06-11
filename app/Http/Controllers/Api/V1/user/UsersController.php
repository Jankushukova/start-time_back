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
        $user['projectsCount'] = Project::whereOwnerId($user->id)->whereActive(1)->count();
        $user['bakersCount'] = count(ProjectOrderController::getUserBakers($user->id));
        $user['bakedCount'] = ProjectOrder::whereUserId($user->id)->where('confirmed', 1)->count();
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
        return response()->json([],200);

    }

    public function closed()
    {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'),200);
    }


    public function passwordReset(Request $request){
        return $request->all();
    }




    public function index(Request $request)
    {
        $users = User::with('projects')->get();
        return CollectionHelper::paginate($users, count($users), $request->perPage);

    }

    public function filter(Request $request){
        $searchText = $request->searchText;
        $users = User::with('projects')
            ->where('users.firstname', 'like', '%' . $searchText . '%')
            ->orWhere('users.lastname', 'like', '%' . $searchText . '%')
            ->get();
        return CollectionHelper::paginate($users, count($users), $request->perPage);
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
        $user['projects'] = Project::whereOwnerId($id)->get()->filter(function ($item, $key){
            error_log($item->active == 1);
            return $item->active != 0;
        })->values();
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

    public function updateAdmin(Request $request){
        $user = User::findOrFail($request->id);
        if($request->firstname != $user->firstname){
            error_log('firstnmae no t==equal');
            $user->firstname = $request->firstname;
        }
        if($request->lastname != $user->lastname){
            error_log('lastname no t==equal');
            $user->lastname = $request->lastname;
        }
        if($request->phone_number != $user->phone_number){
            error_log('phone no t==equal');
            $user->phone_number = $request->phone_number;
        }
        if($request->biography != $user->biography){
            error_log('biography no t==equal');
            $user->biography = $request->biography;
        }
        if($request->email != $user->email){
            error_log('email no t==equal');
            $user->email = $request->email;
            $user->email_verified_at = null;
            $user->sendApiEmailVerificationNotification();
        }
        if($request->role_id != $user->role_id){
            error_log('role not equal');
            $user->role_id = $request->role_id;
        }

        $user->save();
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        if($request->firstname != $user->firstname){
            error_log('firstnmae no t==equal');
            $user->firstname = $request->firstname;
        }
        if($request->lastname != $user->lastname){
            error_log('lastname no t==equal');
            $user->lastname = $request->lastname;
        }
        if($request->phone_number != $user->phone_number){
            error_log('phone no t==equal');
            $user->phone_number = $request->phone_number;
        }
        if($request->biography != $user->biography){
            error_log('biography no t==equal');
            $user->biography = $request->biography;
        }
        if($request->email != $user->email){
            error_log('email no t==equal');
            $user->email = $request->email;
            $user->email_verified_at = null;
            $user->sendApiEmailVerificationNotification();
        }

        $user->save();
        return $user;
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
