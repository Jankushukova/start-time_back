<?php

namespace App\Http\Controllers\API\V1\Product;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\API\V1\PaymentController;
use App\Http\Controllers\Controller;
use App\OrderProduct;
use App\Product;
use App\ProductOrder;
use App\ProductPayment;
use App\ProjectPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductOrderController extends Controller
{
    public function index()
    {
        return ProductOrder::all();
    }
    public function getOrders(Request $request){
        $orders = ProductOrder::with('user','products', 'payment')->whereConfirmed(1)->get();
        return CollectionHelper::paginate($orders, count($orders), $request->perPage);

    }


    public function getOrdersOfProduct($id){
        return Product::findOrFail($id)->ordered;
    }

    public function getPaymentsOfProduct($id){
        return Product::findOrFail($id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImagesOfNews($id)
    {
        return ProductOrder::where('project_id',$id);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = $request->order;
        $products = $request->products;
        error_log('product order store');
        $order = ProductOrder::create($order);
        $order->save();
        foreach ($products as $product){
            $newProduct = new OrderProduct;
            $newProduct->product_id = $product['product_id'];
            $newProduct->count = $product['count'];
            $newProduct->order_id = $order->id;
            $newProduct->save();

        }
        return $order;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return OrderProduct::whereOrderId($id)->with('product')->get();
    }

    public function filter(Request $request){
        $searchText = $request->searchText;
        $attribute = $request->attribute;
        error_log($searchText);
        error_log($attribute);
        if($attribute == 'user'){
            $orders = ProductOrder::select(DB::raw('product_orders.*'))
                ->where('first_name', 'like', '%' . $searchText . '%')
                ->orWhere('last_name', 'like', '%' .$searchText. '%')
                ->where('product_orders.confirmed','=',1)
                ->get();
        }
        if($attribute == 'price') {
            $orders = ProductOrder::select(DB::raw('product_orders.*'))
                ->join('product_payments', 'product_payments.order_id','=', 'product_orders.id')
                ->where('product_payments.sum', 'like' , '%' . $searchText . '%')
                ->where('product_orders.confirmed','=', 1)
                ->get();
        }
        if($attribute == 'product') {
            $orders = ProductOrder::select(DB::raw('product_orders.*'))
                ->join('order_products', 'order_products.order_id', 'product_orders.id')
                ->join('products', 'order_products.product_id', 'products.id')
                ->where('products.title_rus', 'like', '%' . $searchText . '%')
                ->where('product_orders.confirmed','=', 1)
                ->get();
        }
        $orders = $orders->map(function ($item, $key){
           $item->payment;
           $item->products;
           $item->user;
           return $item;
        });

        return CollectionHelper::paginate($orders, count($orders), $request->perPage);

    }

    public function getOrdersOfBank(Request $request){
        $bank_id = $request->id;
        $orders = ProductOrder::select(DB::raw('product_orders.*'))
            ->join('product_payments', 'product_payments.order_id','=','product_orders.id')
            ->where('type_id', $bank_id)
            ->get()->map(function ($item, $key){
                $item->products;
                $item->payment;
                $item->user;
                return $item;
            });
        return CollectionHelper::paginate($orders, count($orders), $request->perPage);
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
        $order = ProductOrder::findorFail($id);
        $order->update($request->all());
        return $order;
    }

    public function payment(Request $request){
        $order = ProductOrder::findOrFail($request->order_id);
        $url = PaymentController::epayBasicAuth($order, $request->sum);
        return response()->json(['url'=>$url], 200);

    }

    public function cloudSuccess(Request $request){
        error_log('order id');
        error_log($request->order_id);
        $order = ProductOrder::findOrFail($request->order_id);
        $order->confirmed = 1;
        $payment =  new ProductPayment();
        error_log($request->sum);
        $payment->sum = $request->sum;
        $payment->type_id = ProductPayment::CLOUD;
        $payment->order_id = $request->order_id;
        $payment->save();
        $order->confirmed = 1;
        $order->save();
        return $order;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = ProductOrder::findOrFail($id);
        $order->delete();
        return response()->json(['success' => true]);
    }
}
