<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\ProjectOrder;
use App\ProjectPayment;
use Dosarkz\EPayKazCom\Facades\Epay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;


class PaymentController extends Controller
{
    public static function epayBasicAuth($order, $sum){
        $pay =  Epay::basicAuth([
            'order_id' => $order->id,
            'currency' => '398',
            'amount' => $sum,
            'email' => $order->email,
            'phone_number' => $order->phone_number,
            'hashed' => true,
        ]);
        $url = $pay->generateUrl();
        return $url;
    }

    public function checkPay(){
        $checkPay = Epay::checkPay( [ 'order_id' => '1' ] );

        $response = Epay::request( $checkPay->generateUrl() );

        return $response;
    }

    public function controlPay(){
        $controlPay = Epay::controlPay( [
            'order_id' => '01111111111',
            'amount' => 9999,
            'approval_code' => '170407',
            'reference' => '180711170407',
            'currency' => '398',
            'command_type' => 'complete',
            'reason' => 'for test'
        ] );

        $response = Epay::request( $controlPay->generateUrl() );
        return $response;
    }

    public function epaySuccess(Request $request){
        error_log('success');
    }

    public function epayFailure(Request $request){
        error_log("failed payment");
    }

    public function cloudSuccess(Request $request){
        error_log('cloud');
        error_log('success');
    }

    public function cloudFailure(Request $request){
        error_log("cloud payment");
        error_log("failed payment");
    }


}
