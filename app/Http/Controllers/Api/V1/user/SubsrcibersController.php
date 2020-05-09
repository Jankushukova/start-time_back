<?php

namespace App\Http\Controllers;

use App\Subsrcibers;
use Illuminate\Http\Request;

class SubsrcibersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Subsrcibers::all();
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
        $sb = Subsrcibers::create($request->all());
        $sb->save();
        return $sb;    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subsrcibers  $subsrcibers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Subsrcibers::findorFail($id);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subsrcibers  $subsrcibers
     * @return \Illuminate\Http\Response
     */
    public function edit(Subsrcibers $subsrcibers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subsrcibers  $subsrcibers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sb = Subsrcibers::findorFail($id);
        $sb->update($request->all());
        return $sb;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subsrcibers  $subsrcibers
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sb = Subsrcibers::findOrFail($id);
        $sb->delete();
        return response()->json(['success' => true]);
    }
}
