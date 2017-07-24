<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Class to work with table "orders"
 */

use Respect\Validation\Validator as v;

require_once __DIR__ . "/PspTableDefinitions.php";

class PspOrders extends PspOrdersTable
{

    function validate()
    {
        $input = [
            "id" => v::length(16, 16),
            "currency" => v::length(3, 3),
            "amount" => v::numeric()->positive(),
            "description" => v::length(1, 1024),
            "payer_firstname" => v::length(1, 32),
            "payer_lastname" => v::length(1, 32),
            "payer_address" => v::length(1, 255),
            "payer_country" => v::length(2, 2),
            "payer_state" => v::length(2, 2),
            "payer_city" => v::length(2, 16),
            "payer_zip" => v::length(2, 32),
            "payer_email" => v::email(),
            "payer_phone" => v::length(1, 32),
            "payer_ip" => v::notEmpty(),
            "status" => v::notEmpty(),
            "hash_p1" => v::length(10, 10),
            "create_at" => v::date('Y-m-d H:i:s'),
            "update_at" => v::date('Y-m-d H:i:s')
        ];

        /**
         * @var $validator \Respect\Validation\Validator
         */
        foreach ($input as $field => $validator) {
            if (!$validator->validate($this->{$field})) {
                throw new Exception("{$field} is not valid");
            }
        }

    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->update_at = dbtime();
        $this->update();
    }

    public function getRemoteStatus()
    {
        $transId = $this->getTransId();

        $hashParts = [
            strrev($this->payer_email),
            cfg()->ikajo->clientPass,
            $transId,
            $this->hash_p1
        ];

        $payload = [
            'action' => 'GET_TRANS_STATUS',
            'client_key' => cfg()->ikajo->clientKey,
            'trans_id' => $transId,
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
        return $data;
    }

    public function getTransId()
    {
        $orderFlow = PspOrdersFlow::findRow('order_id = ?', $this->id);
        if ($orderFlow) {
            return $orderFlow->trans_id;
        }

        return "";
    }

    /**
     * Generate 16-digit UUID for order id
     * @return string
     */
    static function generateUUID()
    {
        $parts = [
            abs(crc32(uniqid() . cfg()->secret)),
            abs(crc32(microtime() . cfg()->secret))
        ];

        return substr(join("", $parts), 0, 16);
    }
}