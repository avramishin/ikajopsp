<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Callback notifications controller
 */

$log = new AirLog(storage_path("logs/debug/" . date('Y-m-d') . "/ikajo/notify.log"));

try {

    $log->writeLn(print_r($_REQUEST, true));

    if (!$request = file_get_contents('php://input')) {
        throw new Exception("Request body is empty");
    }

    $log->writeLn($request);

    if ($json = json_decode($request)) {
        throw new Exception("Request object is empty");
    }

    if (empty($json->action) || empty($json->result)) {
        throw new Exception("Invalid request object");
    }

    $handler = new IkajoResponseHandler($json, $checkHash = true);

    echo "OK";
} catch (Exception $e) {
    $log->writeLn($e->getMessage());
    $log->writeLn($e->getTraceAsString());
    echo "ERROR";
}
