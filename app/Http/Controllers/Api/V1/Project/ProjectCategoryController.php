<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectCategoryController extends Controller
{
    public function __construct()
    {
    }
    public function getAllCategories(Request $request){
        $categories = ProjectCategory::with('projects')->get();
        return CollectionHelper::paginate($categories, count($categories), $request->perPage);

    }

    public function index()
    {
        return ProjectCategory::all();
    }


    public function getProductsOfCategory($id){

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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $projectCategory = ProjectCategory::create($request->all());
        $projectCategory->save();
        return $projectCategory;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return ProjectCategory::findorFail($id);
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
        $projectCategory = ProjectCategory::findorFail($id);
        $projectCategory->update($request->all());
        return $projectCategory;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $projectCategory = ProjectCategory::findOrFail($id);
        $projectCategory->delete();
        return response()->json(['success' => true]);
    }
}
