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

    $status = $order->getRemoteStatus();

    echo $status;

} catch (Exception $e) {
    $log->writeLn($e->getMessage());
    $log->writeLn($e->getTraceAsString());
    echo $e->getMessage();
}
