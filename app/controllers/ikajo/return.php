<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * 3DS return controller
 */

$log = new AirLog(storage_path("logs/debug/" . date('Y-m-d') . "/ikajo/return.log"));

try {

    $log->writeLn(print_r($_REQUEST, true));

    if (!$order = PspOrders::get(r('id'))) {
        throw new Exception("Order not found!");
    }

    if($status = $order->getRemoteStatus()){
        $order->setStatus($status);
    }

    if ($status == 'SETTLED') {
        header("location: " . $order->success_url);
    } else {
        throw new Exception("Payment status: {$status}");
    }

} catch (Exception $e) {
    $log->writeLn($e->getMessage());
    $log->writeLn($e->getTraceAsString());

    if (!empty($order->error_url)) {
        header("location: " . $order->error_url . "?" . http_build_query(['msg' => $e->getMessage()]));
    } else {
        header("location: " . url("ikajo/error", ['msg' => $e->getMessage()]));
    }
}