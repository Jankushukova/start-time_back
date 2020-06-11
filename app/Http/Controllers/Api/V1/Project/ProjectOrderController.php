<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Exports\DonationExport;
use App\Helpers\CollectionHelper;

use App\Http\Controllers\API\V1\PaymentController;
use App\Http\Controllers\Controller;
use App\ProjectPayment;
use App\PaymentType;
use App\Project;
use App\ProjectOrder;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use function Couchbase\basicEncoderV1;

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
            ->where('project_orders.confirmed', 1)
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
        return ProjectOrder::whereIn('project_id',$ids)->where('confirmed', 1)->get();
    }

    public function getBakersOfProject($id)
    {
        return ProjectOrder::where('project_id',$id)->where('confirmed', 1)->get();
    }

    public function getAllBakers(Request $request){
        $bakers = ProjectPayment::with('order', 'bank' )->get()->map(function ($item, $key){
            $item->order->gift;
            $item->order->project;
            return $item;
        });
        return CollectionHelper::paginate($bakers, count($bakers), $request->perPage);
    }


    public function filterAllBakers(Request $request){
        $searchText = $request->searchText;
        $attribute = $request->attribute;
        error_log($searchText);
        error_log($attribute);
        if($attribute==='title_rus'){
            $bakers = ProjectPayment::with('order', 'bank' )
                ->join('project_orders', 'project_orders.id', '=', 'project_payments.order_id')
                ->join('projects', 'projects.id', '=', 'project_orders.project_id')
                ->where('projects.title_rus', 'like', '%' . $searchText . '%');
        }
        else if ($attribute === 'sum'){
            $bakers = ProjectPayment::with('order', 'bank' )
                ->where('project_payments.sum', 'like', '%' . $searchText . '%');
        }else{
            $bakers = ProjectPayment::with('order', 'bank' )
                ->join('project_orders', 'project_orders.id', '=', 'project_payments.order_id')
                ->where('project_orders.' . $attribute, 'like', '%' . $searchText . '%');
        }

        $bakers = $bakers->get()->map(function ($item, $key){
            $item->order->gift;
            $item->order->project;
            return $item;
        });
        return CollectionHelper::paginate($bakers, count($bakers), $request->perPage);
    }

    public function getOrdersOfBank(Request $request){
        $bank_id = $request->id;
        $bakers = ProjectPayment::with('order', 'bank' )
            ->where('type_id', $bank_id)
            ->get()->map(function ($item, $key){
            $item->order->gift;
            $item->order->project;
            return $item;
        });
        return CollectionHelper::paginate($bakers, count($bakers), $request->perPage);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeEpay(Request $request)
    {
        error_log($request->gift_id);
        $order = ProjectOrder::create($request->all());
        $order->save();
        error_log('epaay');
        $url = PaymentController::epayBasicAuth($order, $request->sum);
        return response()->json(['url'=>$url], 200);
    }

    public function store(Request $request){
        $order = ProjectOrder::create($request->all());
        $order->save();
        return $order;
    }

    public function cloudSuccess(Request $request){
        error_log('order id');
        error_log($request->id);
        $order = ProjectOrder::findOrFail($request->id);
        $payment =  new ProjectPayment;
        error_log($request->sum);
        error_log($request->paymentType);
        $payment->sum = $request->sum;
        $payment->type_id = $request->paymentType;
        $payment->order_id = $request->id;
        $payment->save();
        $order->confirmed = 1;
        return $order;
    }
    public function cloudFailure(Request $request){
        error_log('Failure cloud');
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

    public function export()
    {
        return Excel::download(new DonationExport(), Carbon::now().'start-time-bakers.xlsx');
    }
}
