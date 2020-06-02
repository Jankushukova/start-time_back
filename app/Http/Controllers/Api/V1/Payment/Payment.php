<?php

namespace App\Http\Controllers\Api\V1\Payment;

use Illuminate\Http\Request;

abstract class Payment
{
    const METHOD_DEFAULT = 0;
    const METHOD_CARD_PARKING = 1;
    const METHOD_CARD_CINEMAX = 2;

    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_MISSING = 3;
    const STATUS_NOT_PAID = 4;
    const STATUS_ERROR = 5;

    abstract public function pay($method, $order_id, $amount, $description, $email, $post_link, $back_link, $failure_link, $check_link = null, $user_id = null, $card_id = null);

    abstract public function process(Request $request);

    abstract public function confirm($order_id, $amount, $reference, $approval_code);

    abstract public function status($order_id);

    abstract public function return($order_id, $amount, $reference, $reverse = false, $reason_param = null);

    abstract public function respond_success($script, $description, $timeout = null);

    abstract public function respond_error($script, $status, $description, $error_code = null);

    abstract public function card_add($user_id, $order_id, $post_link, $back_link, $failure_link);

    abstract public function card_process(Request $request);

    abstract public function card_list($user_id);

    abstract public function card_delete($user_id, $card_id);

    abstract public function card_confirm($user_id, $card_id, $reference = null, $back_link = null);

    abstract public function card_pay($method, $order_id, $amount, $user_id, $card_id);

    public function getClassBaseName()
    {
        return class_basename($this);
    }

    protected function getMerchantId($method)
    {
        switch ($method)
        {
            case self::METHOD_CARD_PARKING:
                return env('KKB_PARKING_MERCHANT_ID');
            case self::METHOD_CARD_CINEMAX:
                return env('KKB_CINEMAX_MERCHANT_ID');
            default:
                return env('KKB_MERCHANT_ID');
        }
    }
}

