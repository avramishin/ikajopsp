<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Create new Sale controller
 */
use Respect\Validation\Validator as v;

$log = new AirLog(storage_path("logs/debug/" . date('Y-m-d') . "/ikajo/sale/create.log"));

try {

    $log->writeLn(json_encode($_REQUEST, JSON_PRETTY_PRINT));

    $errorUrl = r('error_url', url("ikajo/error"));
    $successUrl = r('success_url', url("ikajo/success"));

    /**
     * Create and insert order
     */
    $order = new PspOrders();
    $order->id = PspOrders::generateUUID();
    $order->channel_id = r('channel_id', cfg()->ikajo->defaultChannelId);
    $order->currency = r('order_currency', cfg()->ikajo->defaultCurrency);
    $order->amount = sprintf("%.2f", r('order_amount'));
    $order->description = r('description', "Order payment");
    $order->payer_firstname = r('payer_first_name');
    $order->payer_lastname = r('payer_last_name');
    $order->payer_address = r('payer_address');
    $order->payer_country = r('payer_country');
    $order->payer_state = r('payer_state', 'NA');
    $order->payer_city = r('payer_city');
    $order->payer_zip = r('payer_zip');
    $order->payer_email = r('payer_email');
    $order->payer_phone = r('payer_phone');
    $order->payer_ip = $_SERVER['REMOTE_ADDR'];
    $order->error_url = $errorUrl;
    $order->success_url = $successUrl;
    $order->status = 'INIT';
    $order->async = 'N';
    $order->auth = 'N';
    $order->hash_p1 = strrev(substr(r('card_number'), 0, 6) . substr(r('card_number'), -4));
    $order->create_at = dbtime();
    $order->update_at = dbtime();

    $order->validate();
    $order->insert();

    $log->writeLn("Order ID={$order->id} created");

    $hashParts = [
        strrev($order->payer_email),
        cfg()->ikajo->clientPass,
        $order->hash_p1
    ];

    $cardExpYear = r('card_exp_year');
    if (strlen($cardExpYear) == 2) {
        $cardExpYear .= "20";
    }

    if (!v::length(16, 16)->validate(r('card_number'))) {
        throw new Exception("card number is not valid");
    }

    if (!v::length(2, 2)->validate(r('card_exp_month'))) {
        throw new Exception("expiration month is not valid");
    }

    if (!v::length(4, 4)->validate($cardExpYear)) {
        throw new Exception("expiration year is not valid");
    }

    if (!v::length(3, 4)->validate(r('card_cvv2'))) {
        throw new Exception("cvv2 is not valid");
    }

    $payload = [
        'action' => 'SALE',
        'client_key' => cfg()->ikajo->clientKey,
        'order_id' => $order->id,
        'order_amount' => $order->amount,
        'order_currency' => $order->currency,
        'order_description' => $order->description,
        'card_number' => r('card_number'),
        'card_exp_month' => r('card_exp_month'),
        'card_exp_year' => $cardExpYear,
        'card_cvv2' => r('card_cvv2'),
        'payer_first_name' => $order->payer_firstname,
        'payer_last_name' => $order->payer_lastname,
        'payer_address' => $order->payer_address,
        'payer_country' => $order->payer_country,
        'payer_state' => $order->payer_state,
        'payer_city' => $order->payer_city,
        'payer_zip' => $order->payer_zip,
        'payer_email' => $order->payer_email,
        'payer_phone' => $order->payer_phone,
        'payer_ip' => $order->payer_ip,
        'term_url_3ds' => url('ikajo/return', [
            'id' => $order->id
        ]),
        'hash' => md5(strtoupper(join('', $hashParts)))
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, cfg()->ikajo->billingUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $data = curl_exec($ch);

    if ($error = curl_error($ch)) {
        throw new Exception($error);
    }

    if (!$json = json_decode($data)) {
        throw new Exception("Request object is empty");
    }

    if (empty($json->result)) {
        throw new Exception("Invalid request object");
    }

    if ($json->result == 'ERROR') {
        throw new Exception($json->error_message);
    }

    $handler = new IkajoResponseHandler($json, $checkHash = false);

    if ($redirect = $handler->getRedirect()) {
        echo view("app/views/3ds/redirect.twig", [
            'action' => $redirect['url'],
            'method' => $handler->getRedirectMethod(),
            'params' => $redirect['params']
        ]);
    } elseif ($handler->getStatus() == 'DECLINED') {
        throw new Exception($handler->getDeclineReason());
    } else {
        header("location: " . $successUrl);
    }

} catch (Exception $e) {
    $log->writeLn($e->getMessage());
    $log->writeLn($e->getTraceAsString());
    header("location: " . $errorUrl . "?" . http_build_query(['msg' => $e->getMessage()]));
}