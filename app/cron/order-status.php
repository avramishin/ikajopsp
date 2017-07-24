<?php
require_once __DIR__ . "/../../bootstrap.php";
if ($id = $argv[1]) {
    if ($order = PspOrders::get($id)) {
        echo $order->getRemoteStatus();
    }
}
