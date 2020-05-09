<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Project;
use App\Update;
use Illuminate\Http\Request;

class UpdatesController extends Controller
{
    public function __construct()
    {
    }

    public function getUpdatesOfProject($id){
        return Project::findOrFail($id)->updates;
    }

    public function getUpdatesImages($id){
        return Update::findOrFail($id)->images;
    }
    public function index()
    {
        return Update::all();
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

    public function getCurrent()
    {
        return Auth::user();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Update::create($request->all());
        $user->role_id = Role::CLIENT_ID;

        $user->password = bcrypt($user->password);;
        $user->save();
        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return Update::findorFail($id);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update = Update::findorFail($id);
        $update->update($request->all());
        return $update;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $update = Update::findOrFail($id);
        $update->delete();
        return response()->json(['success' => true]);
    }
}
