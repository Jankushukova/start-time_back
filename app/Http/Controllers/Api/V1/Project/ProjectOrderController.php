<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\API\V1\PaymentController;
use App\Http\Controllers\Controller;
use App\ProjectPayment;
use App\PaymentType;
use App\Project;
use App\ProjectOrder;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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


    public static function getUserBakers($id){
        return User::all()
            ->whereIn('id',( Project::select(DB::raw('project_orders.user_id'))
            ->join('project_orders', 'projects.id','=','project_orders.project_id')
            ->where('projects.owner_id', $id)
            ->get()->map(function($item,$key){
                return $item['user_id'];
            })))->values();
    }

//    public function getPaymentsOfProject($id){
//        return Project::findOrFail($id)->payments;
//    }




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

    public function getAllBakers(Request $request){
        $bakers = ProjectPayment::with('order', 'bank' )->get()->map(function ($item, $key){
            $item->order->gift;
            $item->order->project;
            return $item;
        });
        return CollectionHelper::paginate($bakers, count($bakers), $request->perPage);

//        $users = ProjectOrder::with('user', 'project', 'payment')->get()->values()->map(function ($item, $key){

//            $item->payment->first()->bank;
//
//            $item = [
//                'user' => $item->user->first(),
//                'payment' => $item->payment->first(),
//                'project' => $item->project->first(),
//                'gift' => $bakersGift,
//            ];
//            return $item;
//        });
//
//        return CollectionHelper::paginate($users, count($users), $request->perPage);

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        error_log($request->gift_id);
        $order = ProjectOrder::create($request->all());
        $order->save();
        $url = PaymentController::basicAuth($order, $request->sum);
        return response()->json(['url'=>$url], 200);
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
