<?php

namespace App\Http\Controllers\Api\V1\Payment;


use App\Http\Controllers\Api\V1\Payment\KKB\KKB;
use App\Http\Controllers\Api\V1\Payment\PayBoxMoney\PayBoxMoney;

class PaymentFactory
{
    public static function instance($processor = null)
    {
        if ($processor == null)
            $processor = env('PAYMENT_PROCESSOR');

        switch ($processor)
        {
            case "KKB":
                return new KKB();
                break;
            case "PayBoxMoney":
                return new PayBoxMoney();
                break;
            default:
                return null;
        }
    }
}
