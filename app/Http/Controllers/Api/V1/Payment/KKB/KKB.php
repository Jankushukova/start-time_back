<?php

namespace App\Http\Controllers\Api\V1\Payment\KKB;


use App\Http\Controllers\Api\V1\Payment\Payment;
use App\Http\Controllers\Api\V1\Payment\PaymentException;
use Illuminate\Support\Facades\File;

class KKB extends Payment
{
    public function pay($method, $order_id, $amount, $description, $email, $post_link, $back_link, $failure_link, $check_link = null, $user_id = null, $card_id = null)
    {
        if ($user_id != null && $card_id != null)
            $merchant_id = $this->getMerchantId($method);
        else
            $merchant_id = env('KKB_MERCHANT_ID');

        $cert_id = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_cert_id');
        $merchant_name = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_name');
        $keypass = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/password');
        $currency = 398;

        $xml = new \SimpleXMLElement('<document></document>');

        $merchant = $xml->addChild('merchant');
        $merchant->addAttribute('cert_id', $cert_id);
        $merchant->addAttribute('name', $merchant_name);

        $order = $merchant->addChild('order');
        $order->addAttribute('order_id', $order_id);
        $order->addAttribute('amount', $amount);
        $order->addAttribute('currency', $currency);

        $department = $order->addChild('department');
        $department->addAttribute('amount', $amount);

        if ($user_id != null && $card_id != null)
        {
            $department->addAttribute('abonent_id', $user_id);
            $department->addAttribute('card_id', $card_id);
            $department->addAttribute('main', env('KKB_CARD_MERCHANT_ID'));
            $department->addAttribute('benef', $merchant_id);
        }
        else
        {
            $department->addAttribute('merchant_id', $merchant_id);
        }

        $signer = new KKBSign();
        $signer->invert();
        $signer->load_private_key(storage_path() . "/keys/kkb/" . $merchant_id . "/cert.prv", $keypass);
        $xml_signature = $signer->sign64($xml->merchant[0]->asXML());

        $merchant_sign = $xml->addChild('merchant_sign');
        $merchant_sign->addAttribute('type', "RSA");
        $merchant_sign[0] = $xml_signature;

        $appendix = new \SimpleXMLElement('<document></document>');
        $item = $appendix->addChild('item');
        $item->addAttribute('name', "Билет");
        $item->addAttribute('description', $description);
        $item->addAttribute('quantity', 1);
        $item->addAttribute('amount', $amount);

        $url = env('KKB_LOGON_URL');

        return view('payment.kkb.redirect')->with('url', $url)
            ->with('signed_order_b64', base64_encode(preg_replace('!^[^>]+>(\r\n|\n)!','', $xml->asXML())))
            ->with('email', $email)
            ->with('appendix', base64_encode(preg_replace('!^[^>]+>(\r\n|\n)!','', $appendix->asXML())))
            ->with('post_link', $post_link)
            ->with('back_link', $back_link)
            ->with('failure_link', $failure_link);
    }

    public function process(Request $request)
    {
        $xml = simplexml_load_string($request->get('response'), "SimpleXMLElement", LIBXML_NOCDATA);

        $signature = (string)$xml->bank_sign;
        $signature = str_replace(' ', '+', $signature);

        $signer = new KKBSign();
        $signer->invert();
        if (!$signer->check_sign64($xml->bank[0]->asXML(), $signature, storage_path() . "/keys/kkb/kkbca.pub"))
            throw new PaymentException("", PaymentException::INVALID_SIGNATURE);

        $order_id = (string)$xml->bank->customer->merchant->order['order_id'];
        $status = (string)$xml->bank->results->payment['response_code'];
        $merchant_id = (int)$xml->bank->results->payment['merchant_id'];
        $amount = (int)$xml->bank->results->payment['amount'];
        $approval_code = (int)$xml->bank->results->payment['approval_code'];
        $reference = (string)$xml->bank->results->payment['reference'];

        if ($status !== "00")
            throw new PaymentException($this->parseErrorCode($status), PaymentException::PAYMENT_REJECTED);

        return [
            'merchant_id' => $merchant_id,
            'order_id' => $order_id,
            'amount' => $amount,
            'reference' => $reference,
            'approval_code' => $approval_code
        ];
    }

    public function confirm($order_id, $amount, $reference, $approval_code)
    {
        $merchant_id = env('KKB_MERCHANT_ID');
        $currency = 398;
        $cert_id = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_cert_id');
        $keypass = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/password');

        $xml = new \SimpleXMLElement('<document></document>');

        $merchant = $xml->addChild('merchant');
        $merchant->addAttribute('id', $merchant_id);

        $command = $merchant->addChild('command');
        $command->addAttribute('type', "complete");

        $payment = $merchant->addChild('payment');
        $payment->addAttribute('orderid', $order_id);
        $payment->addAttribute('amount', $amount);
        $payment->addAttribute('currency', $currency);
        $payment->addAttribute('reference', $reference);

        $signer = new KKBSign();
        $signer->invert();
        $signer->load_private_key(storage_path() . "/keys/kkb/" . $merchant_id . "/cert.prv", $keypass);
        $xml_signature = $signer->sign64($xml->merchant[0]->asXML());

        $merchant_sign = $xml->addChild('merchant_sign');
        $merchant_sign->addAttribute('cert_id', $cert_id);
        $merchant_sign[0] = $xml_signature;

        $url = env('KKB_CONTROL_URL') . '?' . urlencode($xml->asXML());
        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('GET', $url, [
            'http_errors' => false
        ]);

        if ($response_raw->getStatusCode() != 200)
            throw new PaymentException("", PaymentException::SERVICE_UNAVAILABLE);

        $response_body = $response_raw->getBody();
        $response_xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        $error = (string)$response_xml[0];
        if ($error != null)
            throw new PaymentException($error, PaymentException::CONFIRMATION_FAILED);

        $signature = (string)$response_xml->bank_sign;
        $status = (string)$response_xml->bank->response['code'];
        $message = (string)$response_xml->bank->response['message'];

        $signer = new KKBSign();
        $signer->invert();
        if (!$signer->check_sign64($response_xml->bank[0]->asXML(), $signature, storage_path() . "/keys/kkb/kkbca.pub"))
            throw new PaymentException("", PaymentException::INVALID_SIGNATURE);

        if ($status !== "00")
            throw new PaymentException($message, PaymentException::CONFIRMATION_FAILED);

        return true;
    }

    public function status($order_id)
    {
        $merchant_id = env('KKB_MERCHANT_ID');
        $cert_id = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_cert_id');
        $keypass = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/password');

        $xml = new \SimpleXMLElement('<document></document>');

        $merchant = $xml->addChild('merchant');
        $merchant->addAttribute('id', $merchant_id);

        $order = $merchant->addChild('order');
        $order->addAttribute('id', $order_id);

        $signer = new KKBSign();
        $signer->invert();
        $signer->load_private_key(storage_path() . "/keys/kkb/" . $merchant_id . "/cert.prv", $keypass);
        $xml_signature = $signer->sign64($xml->merchant[0]->asXML());

        $merchant_sign = $xml->addChild('merchant_sign');
        $merchant_sign->addAttribute('cert_id', $cert_id);
        $merchant_sign[0] = $xml_signature;

        $url = env('KKB_STATUS_URL') . '?' . urlencode($xml->asXML());
        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('GET', $url, [
            'http_errors' => false
        ]);

        if ($response_raw->getStatusCode() != 200)
            throw new PaymentException("", PaymentException::SERVICE_UNAVAILABLE);

        $response_body = $response_raw->getBody();
        $response_xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        $error = (string)$response_xml[0];
        if ($error != null)
            throw new PaymentException($error, PaymentException::STATUS_FAILED);

        $signature = (string)$response_xml->bank_sign;
        $payment = (bool)$response_xml->bank->merchant->response['payment'];
        $status = (int)$response_xml->bank->merchant->response['status'];
        $result = (int)$response_xml->bank->merchant->response['result'];

        $signer = new KKBSign();
        $signer->invert();
        if (!$signer->check_sign64($response_xml->bank[0]->asXML(), $signature, storage_path() . "/keys/kkb/kkbca.pub"))
            throw new PaymentException("", PaymentException::INVALID_SIGNATURE);

        if ($payment && $status === 0 && $result === 0)
            return self::STATUS_PENDING;
        else if ($payment && $status === 2 && $result === 0)
            return self::STATUS_PAID;
        else if (!$payment && $status === 2)
            return self::STATUS_PAID;
        else if (!$payment && $status === 7 && $result === 7)
            return self::STATUS_MISSING;
        else if ($payment && $status === 8 && $result === 8)
            return self::STATUS_NOT_PAID;
        else
            return self::STATUS_ERROR;
    }

    public function return($order_id, $amount, $reference = null, $reverse = false, $reason_param = null)
    {
        $merchant_id = env('KKB_MERCHANT_ID');
        $currency = 398;
        $cert_id = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_cert_id');
        $keypass = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/password');

        $xml = new \SimpleXMLElement('<document></document>');

        $merchant = $xml->addChild('merchant');
        $merchant->addAttribute('id', $merchant_id);

        $command = $merchant->addChild('command');
        if ($reverse)
            $command->addAttribute('type', "reverse");
        else
            $command->addAttribute('type', "refund");

        $payment = $merchant->addChild('payment');
        $payment->addAttribute('orderid', $order_id);
        $payment->addAttribute('amount', $amount);
        $payment->addAttribute('currency', $currency);
        $payment->addAttribute('reference', $reference);

        $reason = $merchant->addChild('reason');
        $reason[0] = $reason_param;

        $signer = new KKBSign();
        $signer->invert();
        $signer->load_private_key(storage_path() . "/keys/kkb/" . $merchant_id . "/cert.prv", $keypass);
        $xml_signature = $signer->sign64($xml->merchant[0]->asXML());

        $merchant_sign = $xml->addChild('merchant_sign');
        $merchant_sign->addAttribute('cert_id', $cert_id);
        $merchant_sign[0] = $xml_signature;

        $url = env('KKB_CONTROL_URL') . '?' . urlencode($xml->asXML());
        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('GET', $url, [
            'http_errors' => false
        ]);

        if ($response_raw->getStatusCode() != 200)
            throw new PaymentException("", PaymentException::SERVICE_UNAVAILABLE);

        $response_body = $response_raw->getBody();
        $response_xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        $error = (string)$response_xml[0];
        if ($error != null)
            throw new PaymentException($error, PaymentException::RETURN_FAILED);

        $signature = (string)$response_xml->bank_sign;
        $status = (string)$response_xml->response['code'];
        $message = (string)$response_xml->response['message'];

        $signer = new KKBSign();
        $signer->invert();
        if (!$signer->check_sign64($response_xml->bank[0]->asXML(), $signature, storage_path() . "/keys/kkb/kkbca.pub"))
            throw new PaymentException("", PaymentException::INVALID_SIGNATURE);

        if ($status !== "00")
            throw new PaymentException($message, PaymentException::RETURN_FAILED);

        return true;
    }

    public function respond_success($script, $description, $timeout = null)
    {
        return 0;
    }

    public function respond_error($script, $status, $description, $error_code = null)
    {
        return 0;
    }

    public function card_add($user_id, $order_id, $post_link, $back_link, $failure_link)
    {
        $merchant_id = env('KKB_CARD_MERCHANT_ID');
        $cert_id = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_cert_id');
        $merchant_name = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_name');
        $keypass = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/password');
        $currency = 398;

        $xml = new \SimpleXMLElement('<document></document>');

        $merchant = $xml->addChild('merchant');
        $merchant->addAttribute('cert_id', $cert_id);
        $merchant->addAttribute('name', $merchant_name);

        $order = $merchant->addChild('order');
        $order->addAttribute('order_id', $order_id);
        $order->addAttribute('amount', 0);
        $order->addAttribute('currency', $currency);

        $department = $order->addChild('department');
        $department->addAttribute('merchant_id', $merchant_id);
        $department->addAttribute('amount', 0);
        $department->addAttribute('abonent_id', $user_id);
        $department->addAttribute('approve', 1);

        $signer = new KKBSign();
        $signer->invert();
        $signer->load_private_key(storage_path() . "/keys/kkb/" . $merchant_id . "/cert.prv", $keypass);
        $xml_signature = $signer->sign64($xml->merchant[0]->asXML());

        $merchant_sign = $xml->addChild('merchant_sign');
        $merchant_sign->addAttribute('type', "RSA");
        $merchant_sign[0] = $xml_signature;

        $url = env('KKB_CARD_LOGON_URL');

        return view('payment.kkb.redirect')->with('url', $url)
            ->with('signed_order_b64', base64_encode(preg_replace('!^[^>]+>(\r\n|\n)!','', $xml->asXML())))
            ->with('post_link', $post_link)
            ->with('back_link', $back_link)
            ->with('failure_link', $failure_link);
    }

    public function card_process(Request $request)
    {
        $xml = simplexml_load_string($request->get('response'), "SimpleXMLElement", LIBXML_NOCDATA);

        $signature = (string)$xml->bank_sign;
        $signature = str_replace(' ', '+', $signature);

        $signer = new KKBSign();
        $signer->invert();
        if (!$signer->check_sign64($xml->bank[0]->asXML(), $signature, storage_path() . "/keys/kkb/kkbca.pub"))
            throw new PaymentException("", PaymentException::INVALID_SIGNATURE);

        $status = (string)$xml->bank->results->payment['response_code'];
        $user_id = (int)$xml->bank->results->payment['abonent_id'];

        if ($status !== "00")
            throw new PaymentException($this->parseErrorCode($status), PaymentException::PAYMENT_REJECTED);

        return [
            'user_id' => $user_id
        ];
    }

    public function card_list($user_id)
    {
        $merchant_id = env('KKB_CARD_MERCHANT_ID');
        $cert_id = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_cert_id');
        $keypass = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/password');

        $xml = new \SimpleXMLElement('<document></document>');

        $merchant = $xml->addChild('merchant');
        $merchant->addAttribute('id', $merchant_id);

        $client = $merchant->addChild('client');
        $client->addAttribute('abonent_id', $user_id);
        $client->addAttribute('action', "list");
        $client->addAttribute('recepient', "");
        $client->addAttribute('session', "");

        $signer = new KKBSign();
        $signer->invert();
        $signer->load_private_key(storage_path() . "/keys/kkb/" . $merchant_id . "/cert.prv", $keypass);
        $xml_signature = $signer->sign64($xml->merchant[0]->asXML());

        $merchant_sign = $xml->addChild('merchant_sign');
        $merchant_sign->addAttribute('cert_id', $cert_id);
        $merchant_sign[0] = $xml_signature;

        $url = env('KKB_CARD_CONTROL_URL') . '?' . urlencode($xml->asXML());
        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('GET', $url, [
            'http_errors' => false
        ]);

        if ($response_raw->getStatusCode() != 200)
            throw new PaymentException("", PaymentException::SERVICE_UNAVAILABLE);

        $response_body = $response_raw->getBody();
        $response_xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        $error = (string)$response_xml->error;
        if ($error != null)
            throw new PaymentException($error, PaymentException::CARD_LIST_FAILED);

        $cards_arr = [];
        foreach ($response_xml->cards[0]->children() as $card)
        {
            $card_id = (string)$card['CardID'];
            $masked_number = (string)$card['CardMask'];
            $needs_approval = (string)$card['approve'];
            $reference = (string)$card['reference'];

            $cards_arr[] = [
                'card_id' => $card_id,
                'masked_number' => substr($masked_number, 0, 1) . str_pad(substr($masked_number, -4), 15, '*', STR_PAD_LEFT),
                'approved' => ($needs_approval === 'true') ? 0 : 1,
                'reference' => $reference
            ];
        }

        return $cards_arr;
    }

    public function card_delete($user_id, $card_id)
    {
        $merchant_id = env('KKB_CARD_MERCHANT_ID');
        $cert_id = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_cert_id');
        $keypass = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/password');

        $xml = new \SimpleXMLElement('<document></document>');

        $merchant = $xml->addChild('merchant');
        $merchant->addAttribute('id', $merchant_id);

        $client = $merchant->addChild('client');
        $client->addAttribute('abonent_id', $user_id);
        $client->addAttribute('card_id', $card_id);
        $client->addAttribute('action', "delete");

        $signer = new KKBSign();
        $signer->invert();
        $signer->load_private_key(storage_path() . "/keys/kkb/" . $merchant_id . "/cert.prv", $keypass);
        $xml_signature = $signer->sign64($xml->merchant[0]->asXML());

        $merchant_sign = $xml->addChild('merchant_sign');
        $merchant_sign->addAttribute('cert_id', $cert_id);
        $merchant_sign[0] = $xml_signature;

        $url = env('KKB_CARD_CONTROL_URL') . '?' . urlencode($xml->asXML());
        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('GET', $url, [
            'http_errors' => false
        ]);

        if ($response_raw->getStatusCode() != 200)
            throw new PaymentException("", PaymentException::SERVICE_UNAVAILABLE);

        $response_body = $response_raw->getBody();
        $response_xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        $error = (string)$response_xml->error;
        if ($error != null)
            throw new PaymentException($error, PaymentException::CARD_DELETION_FAILED);

        foreach ($response_xml->cards[0]->children() as $card)
        {
            $status = (string)$card['status'];

            if ($status !== "OK")
                throw new PaymentException("", PaymentException::CARD_DELETION_FAILED);
        }

        return true;
    }

    public function card_confirm($user_id, $card_id, $reference = null, $back_link = null)
    {
        $params = [
            'reference' => $reference,
            'lang' => 'ru',
            'back_link' => $back_link
        ];

        $url = env('KKB_CARD_APPROVE_URL');

        return redirect($url . '?' . http_build_query($params));
    }

    public function card_pay($method, $order_id, $amount, $user_id, $card_id)
    {
        $merchant_id = $this->getMerchantId($method);
        $currency = 398;
        $merchant_name = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_name');
        $cert_id = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/merchant_cert_id');
        $keypass = File::get(storage_path() . '/keys/kkb/' . $merchant_id . '/password');

        $xml = new \SimpleXMLElement('<document></document>');

        $merchant = $xml->addChild('merchant');
        $merchant->addAttribute('name', $merchant_name);
        $merchant->addAttribute('cert_id', $cert_id);

        $order = $merchant->addChild('order');
        $order->addAttribute('order_id', $order_id);
        $order->addAttribute('amount', $amount);
        $order->addAttribute('currency', $currency);
        $order->addAttribute('type', 1);

        $department = $order->addChild('department');
        $department->addAttribute('amount', $amount);
        $department->addAttribute('abonent_id', $user_id);
        $department->addAttribute('card_id', $card_id);
        $department->addAttribute('main', env('KKB_CARD_MERCHANT_ID'));
        $department->addAttribute('benef', $merchant_id);

        $signer = new KKBSign();
        $signer->invert();
        $signer->load_private_key(storage_path() . "/keys/kkb/" . $merchant_id . "/cert.prv", $keypass);
        $xml_signature = $signer->sign64($xml->merchant[0]->asXML());

        $merchant_sign = $xml->addChild('merchant_sign');
        $merchant_sign->addAttribute('type', "RSA");
        $merchant_sign[0] = $xml_signature;

        $url = env('KKB_CARD_PAYMENT_URL');
        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('POST', $url, [
            'http_errors' => false,
            'form_params' => [
                'Signed_Order_B64' => base64_encode($xml->asXML())
            ]
        ]);

        Logger::write('payment.log', $url);
        Logger::write('payment.log', base64_encode($xml->asXML()));

        if ($response_raw->getStatusCode() != 200)
            throw new PaymentException("", PaymentException::SERVICE_UNAVAILABLE);

        $response_body = $response_raw->getBody();
        $response_xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        Logger::write('payment.log', $response_xml->asXML());

        $result = (string)$response_xml->payment['Result'];
        $message = (string)$response_xml->payment['Message'];
        $error_input = (string)$response_xml->error['input'];
        $error_payment = (string)$response_xml->error['payment'];
        $error_system = (string)$response_xml->error['system'];

        if ($error_input != null)
            throw new PaymentException($error_input, PaymentException::PAYMENT_REJECTED);

        if ($error_payment != null)
            throw new PaymentException($error_payment, PaymentException::PAYMENT_REJECTED);

        if ($error_system != null)
            throw new PaymentException($error_system, PaymentException::PAYMENT_REJECTED);

        if ($result !== "00")
            throw new PaymentException($message, PaymentException::PAYMENT_REJECTED);

        return [
            'merchant_id' => $merchant_id
        ];
    }

    private function parseErrorCode($code)
    {
        if ($code === "05" || $code == "57")
            return "Отказ банка-эмитента. Уточните причину отказа.";
        else if ($code === "51")
            return "Недостаточно средств.";
        else if ($code === "14")
            return "Карты не существует (скорей всего неверно введен номер карты).";
        else if ($code === "54")
            return "Карта истекла (срок действия карты истек, либо введен неверно).";
        else if ($code === "-19")
            return "Ошибка авторизации 3DSecure.";
        else if ($code === "61")
            return "Превышение лимита на карте.";
        else if ($code === "99")
            return "Платеж уже возвращен.";
        else
            return "Неизвестно";
    }
}

