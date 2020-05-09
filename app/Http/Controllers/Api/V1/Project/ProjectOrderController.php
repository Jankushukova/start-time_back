<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Payment;
use App\PaymentType;
use App\Project;
use App\ProjectOrder;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectOrderController extends Controller
{
    public function index()
    {
        return ProjectOrder::all();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function getUserBakers($id){
        $projects = (new \App\Http\Controllers\Api\V1\ProjectsController)->getUserProjects($id);
        return $this->getBakersOfProjects($projects);
    }

    public function getPaymentsOfProject($id){
        return Project::findOrFail($id)->payments;
    }

    public function getPaymentsOfProjectOfType($id){
        return Payment::all()->join('payment_type', 'payment.type_id', '=','payment_type.id')
            ->join('project_orders', 'project_orders.payment_id','=','payment.id')
            ->where('payment_type.id', '=', $id);
    }
    public function getPaymentsOfProductOfType($id){
        return Payment::all()->join('payment_type', 'payment.type_id', '=','payment_type.id')
            ->join('product_orders', 'product_orders.payment_id','=','payment.id')
            ->where('payment_type.id', '=', $id);
    }

    public function getUserBakedProjects($id){
        return User::findOrFail($id)->baked;
    }

    public function getBakersOfProjects($ids)
    {
        return ProjectOrder::whereIn('project_id',$ids);
    }

    public function getBakersOfProject($id)
    {
        return ProjectOrder::where('project_id',$id);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $baker = ProjectOrder::create($request->all());
        $baker->save();
        return $baker;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return ProjectOrder::findorFail($id);
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
        $baker = ProjectOrder::findorFail($id);
        $baker->update($request->all());
        return $baker;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $baker = ProjectOrder::findOrFail($id);
        $baker->delete();
        return response()->json(['success' => true]);
    }
}
