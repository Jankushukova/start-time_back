<?php

namespace App\Http\Controllers\API\V1\User;

use App\Follower;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    public function index()
    {
        return Follower::all();
    }


    public function getFollowers($id){
        return User::findOrFail($id)->followers;

    }
    public function getFollowings($id){
        return User::findOrFail($id)->followings;

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImagesOfNews($id)
    {
        return Follower::where('project_id',$id);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $follower = Follower::create($request->all());
        $follower->save();
        return Auth::user();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return Follower::findorFail($id);
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
        $follower = Follower::findorFail($id);
        $follower->update($request->all());
        return $follower;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $follower = Follower::whereFollowedId($id)->whereFollowingId(Auth::user()->id);
        $follower->delete();
        return response()->json(['success' => true]);
    }
}
