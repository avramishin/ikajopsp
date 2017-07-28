<?php 

/**
* Class: PspClientsTable to work with table "clients".
* THIS CLASS WAS AUTOMATICALLY GENERATED. ALL MANUAL CHANGES WILL BE LOST!
* PUT YOUR CODE TO CLASS "PspClients" INSTEAD.
*/
class PspClientsTable extends AirMySqlTable {

    static $fields;
    static $tablename = 'clients';
    static $dbconfig = 'psp';
    static $pk = ['id'];


    /**
    * Field: clients.id mysql type varchar(25)
    * @var string
    */
    public $id;
    
    /**
    * Field: clients.client_key mysql type varchar(100)
    * @var string
    */
    public $client_key;
    
    /**
    * Field: clients.client_pass mysql type varchar(100)
    * @var string
    */
    public $client_pass;
    
    /**
    * Field: clients.default_currency mysql type varchar(3)
    * @var string
    */
    public $default_currency = 'USD';
    
    /**
    * Field: clients.default_channel_id mysql type varchar(50)
    * @var string
    */
    public $default_channel_id;
    
    /**
    * @param mixed $id
    * @return PspClients
    */
    static function get($id)
    {
        return call_user_func_array("parent::get", func_get_args());
    }

    /**
    * @param string $sort_field
    * @return PspClients[]
    */
    static function getAll($sort_field = null)
    {
        return parent::getAll($sort_field);
    }

    /**
    * @param string $where Where clause. For example A::find('id = ?', $id)
    * @return PspClients[]
    */
    static function find($where)
    {
        return call_user_func_array("parent::find", func_get_args());
    }

    /**
    * @param string $where Where clause. For example A::findRow('id = ?', $id)
    * @return PspClients
    */
    static function findRow($where)
    {
        return call_user_func_array("parent::findRow", func_get_args());
    }

}

/**
* Class: PspOrdersTable to work with table "orders".
* THIS CLASS WAS AUTOMATICALLY GENERATED. ALL MANUAL CHANGES WILL BE LOST!
* PUT YOUR CODE TO CLASS "PspOrders" INSTEAD.
*/
class PspOrdersTable extends AirMySqlTable {

    static $fields;
    static $tablename = 'orders';
    static $dbconfig = 'psp';
    static $pk = ['id'];


    /**
    * Field: orders.id mysql type char(16)
    * @var string
    */
    public $id;
    
    /**
    * Field: orders.channel_id mysql type varchar(16)
    * @var string
    */
    public $channel_id;
    
    /**
    * Field: orders.currency mysql type char(3)
    * @var string
    */
    public $currency;
    
    /**
    * Field: orders.amount mysql type decimal(10,2)
    * @var string
    */
    public $amount;
    
    /**
    * Field: orders.description mysql type varchar(255)
    * @var string
    */
    public $description;
    
    /**
    * Field: orders.payer_firstname mysql type varchar(25)
    * @var string
    */
    public $payer_firstname;
    
    /**
    * Field: orders.payer_lastname mysql type varchar(25)
    * @var string
    */
    public $payer_lastname;
    
    /**
    * Field: orders.payer_address mysql type varchar(255)
    * @var string
    */
    public $payer_address;
    
    /**
    * Field: orders.payer_country mysql type varchar(50)
    * @var string
    */
    public $payer_country;
    
    /**
    * Field: orders.payer_state mysql type varchar(50)
    * @var string
    */
    public $payer_state;
    
    /**
    * Field: orders.payer_city mysql type varchar(50)
    * @var string
    */
    public $payer_city;
    
    /**
    * Field: orders.payer_zip mysql type varchar(25)
    * @var string
    */
    public $payer_zip;
    
    /**
    * Field: orders.payer_email mysql type varchar(100)
    * @var string
    */
    public $payer_email;
    
    /**
    * Field: orders.payer_phone mysql type varchar(25)
    * @var string
    */
    public $payer_phone;
    
    /**
    * Field: orders.payer_ip mysql type varchar(15)
    * @var string
    */
    public $payer_ip;
    
    /**
    * Field: orders.status mysql type varchar(25)
    * @var string
    */
    public $status;
    
    /**
    * Field: orders.async mysql type enum('Y','N')
    * @var string
    */
    public $async = 'N';
    
    /**
    * Field: orders.auth mysql type enum('Y','N')
    * @var string
    */
    public $auth = 'N';
    
    /**
    * Field: orders.hash_p1 mysql type char(10)
    * @var string
    */
    public $hash_p1;
    
    /**
    * Field: orders.error_url mysql type varchar(500)
    * @var string
    */
    public $error_url;
    
    /**
    * Field: orders.success_url mysql type varchar(500)
    * @var string
    */
    public $success_url;
    
    /**
    * Field: orders.client_id mysql type varchar(25)
    * @var string
    */
    public $client_id;
    
    /**
    * Field: orders.create_at mysql type datetime
    * @var string
    */
    public $create_at;
    
    /**
    * Field: orders.update_at mysql type datetime
    * @var string
    */
    public $update_at;
    
    /**
    * @param mixed $id
    * @return PspOrders
    */
    static function get($id)
    {
        return call_user_func_array("parent::get", func_get_args());
    }

    /**
    * @param string $sort_field
    * @return PspOrders[]
    */
    static function getAll($sort_field = null)
    {
        return parent::getAll($sort_field);
    }

    /**
    * @param string $where Where clause. For example A::find('id = ?', $id)
    * @return PspOrders[]
    */
    static function find($where)
    {
        return call_user_func_array("parent::find", func_get_args());
    }

    /**
    * @param string $where Where clause. For example A::findRow('id = ?', $id)
    * @return PspOrders
    */
    static function findRow($where)
    {
        return call_user_func_array("parent::findRow", func_get_args());
    }

}

/**
* Class: PspOrdersFlowTable to work with table "orders_flow".
* THIS CLASS WAS AUTOMATICALLY GENERATED. ALL MANUAL CHANGES WILL BE LOST!
* PUT YOUR CODE TO CLASS "PspOrdersFlow" INSTEAD.
*/
class PspOrdersFlowTable extends AirMySqlTable {

    static $fields;
    static $tablename = 'orders_flow';
    static $dbconfig = 'psp';
    static $pk = ['id'];


    /**
    * Field: orders_flow.id mysql type int(10) unsigned
    * @var integer
    */
    public $id;
    
    /**
    * Field: orders_flow.order_id mysql type char(16)
    * @var string
    */
    public $order_id;
    
    /**
    * Field: orders_flow.result mysql type varchar(100)
    * @var string
    */
    public $result;
    
    /**
    * Field: orders_flow.status mysql type varchar(100)
    * @var string
    */
    public $status;
    
    /**
    * Field: orders_flow.trans_id mysql type varchar(100)
    * @var string
    */
    public $trans_id;
    
    /**
    * Field: orders_flow.descriptor mysql type varchar(500)
    * @var string
    */
    public $descriptor;
    
    /**
    * Field: orders_flow.details mysql type text
    * @var string
    */
    public $details;
    
    /**
    * Field: orders_flow.create_at mysql type datetime
    * @var string
    */
    public $create_at;
    
    /**
    * @param mixed $id
    * @return PspOrdersFlow
    */
    static function get($id)
    {
        return call_user_func_array("parent::get", func_get_args());
    }

    /**
    * @param string $sort_field
    * @return PspOrdersFlow[]
    */
    static function getAll($sort_field = null)
    {
        return parent::getAll($sort_field);
    }

    /**
    * @param string $where Where clause. For example A::find('id = ?', $id)
    * @return PspOrdersFlow[]
    */
    static function find($where)
    {
        return call_user_func_array("parent::find", func_get_args());
    }

    /**
    * @param string $where Where clause. For example A::findRow('id = ?', $id)
    * @return PspOrdersFlow
    */
    static function findRow($where)
    {
        return call_user_func_array("parent::findRow", func_get_args());
    }

}