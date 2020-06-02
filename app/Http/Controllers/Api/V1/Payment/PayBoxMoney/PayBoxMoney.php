<?php

namespace App\Http\Controllers\Api\V1\Payment\PayBoxMoney;



use App\Http\Controllers\Api\V1\Payment\Payment;
use App\Http\Controllers\Api\V1\Payment\PaymentException;
use Illuminate\Http\Request;

class PayBoxMoney extends Payment
{
    public function pay($order_id, $amount, $description, $email, $post_link, $back_link, $failure_link, $check_link = null, $user_id = null, $card_id = null)
    {
        $params = [
            'pg_merchant_id' => env('PAYBOXMONEY_MERCHANT_ID'),
            'pg_amount' => $amount,
            'pg_currency' => "KZT",
            'pg_description' => $description,
            'pg_order_id' => $order_id,
            'pg_check_url' => $check_link,
            'pg_result_url' => $post_link,
            'pg_success_url' => $back_link,
            'pg_failure_url' => $failure_link,
            'pg_success_url_method' => "GET",
            'pg_failure_url_method' => "GET",
            'pg_lifetime' => 900,
            'pg_salt' => str_random(16)
        ];

        if ($user_id != null && $card_id != null)
        {
            $params['pg_user_id'] = $user_id;
            $params['pg_card_id'] = $card_id;
        }

        $params['pg_sig'] = $this->sign('payment.php', $params);

        return redirect()->away($this->getLink('payment.php', $params));
    }

    public function process(Request $request)
    {
        $signature = $request->get('pg_sig');
        if ($this->check('result', $request->except('pg_sig'), $signature))
            throw new PaymentException("", PaymentException::INVALID_SIGNATURE);

        if ($request->get('pg_result') != 1)
            throw new PaymentException("Платеж отклонен системой PayBox", PaymentException::PAYMENT_REJECTED);

        return [
            'merchant_id' => env('PAYBOXMONEY_MERCHANT_ID'),
            'order_id' => $request->get('pg_order_id'),
            'amount' => $request->get('pg_amount'),
            'reference' => null,
            'approval_code' => null
        ];
    }

    public function confirm($order_id = null, $amount = null, $reference = null, $approval_code = null) {}

    public function status($order_id)
    {
        $parameters = [
            'pg_merchant_id' => env('PAYBOXMONEY_MERCHANT_ID'),
            'pg_order_id' => $order_id,
            'pg_salt' => str_random(16)
        ];

        $parameters['pg_sig'] = $this->sign('get_status.php', $parameters);

        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('GET', $this->getLink('get_status.php', []), [
            'query' => $parameters,
            'http_errors' => false
        ]);
        $response_body = $response_raw->getBody();
        $xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        $status = (string)$xml->pg_status;
        $salt = (string)$xml->pg_salt;
        $signature = (string)$xml->pg_sig;

        if (!$this->check('get_status.php', ['pg_status' => $status, 'pg_salt' => $salt], $signature))
            throw new PaymentException("", PaymentException::INVALID_SIGNATURE);

        if ($status !== "ok")
            throw new PaymentException("PayBox отклонил запрос на проверку статуса платежа.", PaymentException::STATUS_FAILED);

        return true;
    }

    public function return($order_id, $amount, $reference, $reason_param = null)
    {
        $parameters = [
            'pg_merchant_id' => env('PAYBOXMONEY_MERCHANT_ID'),
            'pg_refund_amount' => $amount,
            'pg_payment_id' => $reference,
            'pg_salt' => str_random(16)
        ];

        $parameters['pg_sig'] = $this->sign('revoke.php', $parameters);

        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('GET', $this->getLink('revoke.php', []), [
            'query' => $parameters,
            'http_errors' => false
        ]);
        $response_body = $response_raw->getBody();
        $xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        $status = (string)$xml->pg_status;
        $salt = (string)$xml->pg_salt;
        $signature = (string)$xml->pg_sig;

        // TODO: Check the fucking signature.
        //if (!$this->check('revoke.php', ['pg_status' => $status, 'pg_salt' => $salt], $signature))
        //    throw new PaymentException("", PaymentException::INVALID_SIGNATURE);

        if ($status !== "ok")
            throw new PaymentException("PayBox отклонил запрос на отмену платежа.", PaymentException::PAYMENT_REJECTED);

        return true;
    }

    public function respond_success($script, $description, $timeout = null)
    {
        $params = array();
        $params['pg_status'] = 'ok';
        $params['pg_description'] = $description;
        $params['pg_salt'] = str_random(16);
        if ($timeout != null)
            $params['pg_timeout'] = $timeout;

        $signature = $this->sign($script, $params);

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response></response>');
        foreach ($params as $key => $value)
            $xml->addChild($key, $value);

        $xml->addChild('pg_sig', $signature);

        return $xml->asXML();
    }

    public function respond_error($script, $status, $description, $error_code = null)
    {
        $params = array();
        $params['pg_status'] = $status;
        $params['pg_salt'] = str_random(16);
        if ($status == 'rejected')
        {
            $params['pg_description'] = $description;
        }
        else
        {
            $params['pg_error_description'] = $description;
            $params['pg_error_code'] = $error_code;
        }

        $signature = $this->sign($script, $params);

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response></response>');
        foreach ($params as $key => $value)
        {
            $xml->addChild($key, $value);
        }

        $xml->addChild('pg_sig', $signature);

        return $xml->asXML();
    }

    public function card_add($user_id, $order_id, $post_link, $back_link, $failure_link)
    {
        $params = [];
        $params['pg_merchant_id'] = env('PAYBOXMONEY_MERCHANT_ID');
        $params['pg_user_id'] = $user_id;
        $params['pg_order_id'] = $order_id;
        $params['pg_post_link'] = $post_link;
        $params['pg_back_link'] = $back_link;
        $params['pg_salt'] = str_random(16);

        $signature = $this->sign('add', $params);
        $params['pg_sig'] = $signature;

        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('POST', "https://api.paybox.money/v1/merchant/" . env('PAYBOXMONEY_MERCHANT_ID') . "/cardstorage/add", [
            'form_params' => $params,
            'http_errors' => false
        ]);
        $response_body = $response_raw->getBody();
        $xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);

        $status = (string)$xml->pg_status;
        if ($status !== "ok")
            return redirect()->away($failure_link);

        $signature = (string)$xml->pg_sig;
        $redirect_url = (string)$xml->pg_redirect_url;

        $response_params = json_decode(json_encode($xml), true);
        if (!$this->check('add', $response_params, $signature))
            return redirect()->away($failure_link);

        return redirect()->away($redirect_url);
    }

    public function card_list($user_id)
    {
        $params = array();
        $params['pg_merchant_id'] = env('PAYBOXMONEY_MERCHANT_ID');
        $params['pg_user_id'] = $user_id;
        $params['pg_salt'] = str_random(16);

        $signature = $this->sign('list', $params);
        $params['pg_sig'] = $signature;

        $client = new \GuzzleHttp\Client();
        $response_raw = $client->request('POST', "https://api.paybox.money/v1/merchant/" . env('PAYBOXMONEY_MERCHANT_ID') . "/cardstorage/list", [
            'form_params' => $params,
            'http_errors' => false
        ]);
        $response_body = $response_raw->getBody();
        $xml = simplexml_load_string($response_body, "SimpleXMLElement", LIBXML_NOCDATA);
        if ($xml == null)
            return [];

        try
        {
            $cards = [];
            foreach ($xml as $card)
            {
                $card_id = (int)$card->pg_card_id;
                $card_pan = (string)$card->pg_card_hash;

                $cards[$card_id] = $card_pan;
            }
        }
        catch (\Exception $e)
        {
            return [];
        }

        return array_reverse(array_filter($cards), true);
    }

    public function card_delete($user_id, $card_id)
    {
        // TODO: Implement card_delete() method.
    }

    private function sign($script, $params)
    {
        $values = array();
        $values[] = $script;

        ksort($params);
        foreach ($params as $key => $value)
            $values[] = $value;

        $values[] = env('PAYBOXMONEY_SECRET_KEY');

        return md5(implode(';', $values));
    }

    private function check($script, $params, $signature)
    {
        $values = array();
        $values[] = $script;

        unset($params['pg_sig']);
        ksort($params);
        foreach ($params as $key => $value)
            $values[] = $value;

        $values[] = env('PAYBOXMONEY_SECRET_KEY');

        if (md5(implode(';', $values)) === $signature)
            return true;

        return false;
    }

    private function getLink($script, $params)
    {
        $query = http_build_query($params);

        return env('PAYBOXMONEY_URL') . '/' . $script . '?' . $query;
    }
}

