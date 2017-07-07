<?php

$response = new AirJsonResponse();

try {

    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM orders WHERE 1 = 1";

    $rpp = r('rows', 30);
    $page = r('page', 1);
    $start = ($page - 1) * $rpp;

    $query = r('query');
    $status = r('status');

    $q = db('psp')
        ->q($sql)
        ->ifQuery($status, 'AND status = ?', $status)
        ->ifQuery($query, 'AND (payer_firstname LIKE ? OR payer_lastname LIKE ?)', '%' . $query . '%', '%' . $query . '%')
        ->orderBy('orders.create_at DESC')
        ->limit($start, $rpp);

    $result = $q->fetchAllObject();
    $total = db('psp')->foundRows();

    $response->total = $total;
    $response->rows = $result;
    $response->send();

} catch (Exception $e) {
    $response->error($e->getMessage());
}