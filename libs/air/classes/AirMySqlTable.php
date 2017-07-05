<?php

/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Parent class for all database classes
 */
class AirMySqlTable
{

    static $pk = [];
    static $generated = [];
    static $tablename;
    static $dbconfig;

    /**
     * Insert object into database
     */
    function insert()
    {
        return self::queryInsert($this)->exec(true);
    }

    /**
     * Update object in database
     */
    function update()
    {
        $db = self::queryUpdate((array)$this);
        static::generateWhere($db, $this->getId());
        $db->exec();
    }

    /**
     * Replace object in database
     */
    function replace()
    {
        self::queryReplace((array)$this)
            ->exec();
    }

    /**
     * Remove object from database
     */
    function remove()
    {
        return static::delete($this->getId());
    }

    /**
     * Init object from post request
     * @param array $ignore array of parameters to skip
     */
    function initPost($ignore = null)
    {
        if ($ignore) $ignore = (array)$ignore;
        foreach ($this as $k => $v) {
            if ($ignore && array_search($k, $ignore) !== false || strpos($k, '_') === 0)
                continue;
            if (isset($_POST[$k])) {
                $val = trim($_POST[$k]);
                $this->{$k} = $val === '' ? null : $val;
            }
        }
    }

    /**
     * Get id for object
     * @return string
     */
    function getId()
    {
        $pk = static::$pk;
        if (!is_array($pk)) return $this->{$pk};
        $key = array();
        foreach ($pk as $field) {
            $key [] = $this->{$field};
        }
        return $key;
    }

    /**
     * Initialize object from parameters
     * @param array $params
     */
    function initObject($params)
    {
        foreach ((array)$params as $k => $v) {
            if ($k != static::$pk && property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    /**
     * Get object from database
     * @param mixed $id
     * @return object
     */
    static function get($id)
    {
        if (is_array($id)) $key = $id;
        else $key = func_get_args();
        $db = static::querySelect();
        static::generateWhere($db, $key);
        return $db->fetchRow(get_called_class());
    }

    /**
     * Get all objects from database
     * @param string $sort_field
     * @return array
     */
    static function getAll($sort_field = null)
    {
        return static::querySelect()
            ->ifQ($sort_field, "ORDER BY $sort_field")
            ->fetchAll(get_called_class());
    }

    /**
     * Find objects
     * @param string $where Where clause. For example A::find('id = ?', $id)
     * @return array
     */
    static function find($where)
    {
        $q = static::querySelect();
        return call_user_func_array([$q, 'where'], func_get_args())
            ->fetchAll(get_called_class());
    }

    /**
     * Find single object
     * @param string $where Where clause. For example A::findRow('id = ?', $id)
     * @return object
     */
    static function findRow($where)
    {
        $q = static::querySelect();
        return call_user_func_array([$q, 'where'], func_get_args())
            ->fetchRow(get_called_class());
    }

    /**
     * Delete row by id
     * @param mixed $key
     * @return integer, affected rows
     */
    static function delete($key)
    {
        $key = is_array($key) ? $key : func_get_args();
        $db = static::queryDelete();
        static::generateWhere($db, $key);
        $db->exec();
        return $db->affectedRows();
    }

    /**
     * Return select query
     * @param string $what
     * @return AirMySqlQuery
     */
    static function querySelect($what = '*')
    {
        $table = static::$tablename;
        return db(static::$dbconfig)
            ->setClass(get_called_class())
            ->select($what)
            ->from($table);
    }

    /**
     * Return AirMySqlQuery delete query
     * @return AirMySqlQuery
     */
    static function queryDelete()
    {
        return db(static::$dbconfig)->deleteFrom(static::$tablename);
    }

    /**
     * Return AirMySqlQuery update query
     * @param array $fields affected fields
     * @return AirMySqlQuery
     */
    static function queryUpdate($fields)
    {
        $db = db(static::$dbconfig)->update(static::$tablename);
        $q = array();
        foreach ($fields as $k => $v) {
            $q [] = $db->quoteName($k) . '=' . $db->quote($v);
        }
        return $db->set(implode(',', $q));
    }

    /**
     * Return AirMySqlQuery replace
     * @param array $fields affected fields
     * @return AirMySqlQuery
     */
    static function queryReplace($fields)
    {
        $db = db(static::$dbconfig)->replace(static::$tablename);
        $q = [];
        foreach ($fields as $k => $v) {
            $q[] = $db->quoteName($k) . '=' . $db->quote($v);
        }
        return $db->set(implode(',', $q));
    }

    /**
     * Return AirMySqlQuery insert query
     * @param array|object $fields affected fields
     * @return AirMySqlQuery
     */
    static function queryInsert($fields)
    {
        $db = db(static::$dbconfig)->insertInto(static::$tablename);
        $q = array();
        foreach ($fields as $k => $v) {
            $q [] = $db->quoteName($k) . '=' . $db->quote($v);
        }
        return $db->set(implode(',', $q));
    }

    /**
     * Generate where statement
     * @param AirMySqlQuery $db
     * @param array|string $key Values for primary key
     * @return AirMySqlQuery
     */
    static function generateWhere($db, $key)
    {
        $key = (array)$key;
        if (count($key) == 1 && is_array($key[0]))
            $key = $key[0];
        if (is_array(static::$pk)) {
            $db->where('1');
            foreach (static::$pk as $i => $pkname) {
                $db->q('AND #? = ?', $pkname, $key[$i]);
            }
        } else {
            $db->where('#? = ?', static::$pk, $key[0]);
        }
        return $db;
    }
}
