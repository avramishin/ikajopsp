<?php

$response = new AirJsonResponse();

try {

    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM orders_flow WHERE 1 = 1";

    $rpp = r('rows', 30);
    $page = r('page', 1);
    $start = ($page - 1) * $rpp;

    $orderId = r('order_id');

    $q = db('psp')
        ->query($sql)
        ->ifQuery($orderId, 'AND order_id = ?', $orderId)
        ->orderBy('orders_flow.create_at DESC')
        ->limit($start, $rpp);

    $result = $q->fetchAllObject();
    $total = db('psp')->foundRows();

    $response->total = $total;
    $response->rows = $result;
    $response->send();

} catch (Exception $e) {
    $response->error($e->getMessage());
}