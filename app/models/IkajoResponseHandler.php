<?php

/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Class IkajoResponseHandler
 */
class IkajoResponseHandler
{
    protected $response;

    function __construct($response, $checkHash = false)
    {
        $this->response = $response;

        if (!method_exists($this, "handle{$this->response->action}")) {
            throw new Exception("TODO: handle action {$this->response->action}");
        }

        if ($checkHash) {
            $this->checkHash();
        }

        call_user_func([$this, "handle{$this->response->action}"]);
    }

    function handleSALE()
    {
        if (in_array($this->response->result, ['SUCCESS', 'DECLINED', 'REDIRECT'])) {
            $this
                ->saveOrderFlow()
                ->getOrder()
                ->setStatus($this->response->status);
        } else {
            throw new Exception("Unexpected result {$this->response->result}");
        }
    }

    /**
     * Get redirect url or empty string
     * @return string
     */
    function getRedirect()
    {
        if (!empty($this->response->redirect_url)) {
            $args = !empty($this->response->redirect_params) ? $this->response->redirect_params : [];
            $args = http_build_query($args);
            return "{$this->response->redirect_url}?{$args}";
        }

        return "";
    }

    /**
     * Get redirect method or empty string
     * @return string
     */
    function getRedirectMethod()
    {
        if (!empty($this->response->redirect_method)) {
            return $this->response->redirect_method;
        }

        return "";
    }

    /**
     * @return PspOrders
     * @throws Exception
     */
    protected function getOrder()
    {
        if (!$order = PspOrders::get($this->response->order_id)) {
            throw new Exception("Order not found!");
        }

        return $order;
    }

    /**
     * @return $this
     */
    protected function saveOrderFlow()
    {
        if ($order = $this->getOrder()) {
            $orderFlow = new PspOrdersFlow();
            $orderFlow->order_id = $order->id;
            $orderFlow->result = !empty($this->response->result) ? $this->response->result : 'NA';
            $orderFlow->status = !empty($this->response->status) ? $this->response->status : 'NA';
            $orderFlow->trans_id = !empty($this->response->trans_id) ? $this->response->trans_id : 'NA';

            if (!empty($this->response->descriptor)) {
                $orderFlow->descriptor = $this->response->descriptor;
            } elseif ($this->response->decline_reason) {
                $orderFlow->descriptor = $this->response->decline_reason;
            } else {
                $orderFlow->descriptor = 'NA';
            }

            $orderFlow->details = json_encode($this->response);
            $orderFlow->create_at = dbtime();
            $orderFlow->insert();
        }

        return $this;
    }

    /**
     * Check request hash
     * @throws Exception
     */
    protected function checkHash()
    {
        $order = $this->getOrder();

        $hashParts = [
            strrev($order->payer_email),
            cfg()->ikajo->clientPass,
            $this->response->trans_id,
            $order->hash_p1
        ];

        $hash = md5(strtoupper(join('', $hashParts)));

        if ($hash != $this->response->hash) {
            throw new Exception("Invalid hash");
        }
    }
}