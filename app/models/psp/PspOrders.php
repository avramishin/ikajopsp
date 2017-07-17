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