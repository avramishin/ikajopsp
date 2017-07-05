<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Class to work with table "orders_flow"
 */

require_once __DIR__ . "/PspTableDefinitions.php";

class PspOrdersFlow extends PspOrdersFlowTable
{
    /**
     * @return PspOrders
     */
    function getOrder()
    {
        return PspOrders::get($this->order_id);
    }
}