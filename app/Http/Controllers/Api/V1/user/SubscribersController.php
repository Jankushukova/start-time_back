<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Subscribers;
use Illuminate\Http\Request;

class SubscribersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Subscribers::all();
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
        $sb = Subscribers::create($request->all());
        $sb->save();
        return $sb;    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subscribers  $subsrcibers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Subscribers::findorFail($id);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subscribers  $subsrcibers
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscribers $subsrcibers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subscribers  $subsrcibers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sb = Subscribers::findorFail($id);
        $sb->update($request->all());
        return $sb;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subscribers  $subsrcibers
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sb = Subscribers::findOrFail($id);
        $sb->delete();
        return response()->json(['success' => true]);
    }
}
