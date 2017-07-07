<?php

/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Class AirMySqlQuery
 */
class AirMySqlQuery
{
    /**
     * @var string
     */
    public $sql = '';
    /**
     * @var null|object
     */
    public $cfg = null;
    /**
     * @var string
     */
    public $classname;


    /**
     * @var mysqli
     */
    public $connection = null;
    /**
     * @var mysqli_result
     */
    public $result = null;

    /**
     * Construct query from configuration or another AirMySqlQuery object
     * @param object $init Configuration object (host, user, password, name) or AirMySqlQuery
     */
    public function __construct($init = null)
    {
        if (is_object($init) && get_class($init) == 'AirMySqlQuery') {
            $this->cfg = $init->cfg;
            $this->connection = $init->connection;
        } else {
            $this->cfg = $init;
            if ($init) {
                $this->connect();
            }
        }
    }

    /**
     * Set class name for created objects
     * @param string $class
     * @return AirMySqlQuery
     */
    public function setClass($class)
    {
        $this->classname = $class;
        return $this;
    }

    /**
     * Append query text and replace placeholders
     * @param string $text
     * @return AirMySqlQuery
     */
    public function query($text)
    {
        if (func_num_args() > 1) {
            $this->sql .= $this->parse($text, array_slice(func_get_args(), 1)) . "\n";
        } else {
            $this->sql .= $text . "\n";
        }
        return $this;
    }

    /**
     * Replace placeholders with values
     * @param string $text
     * @param array $args
     * @return string
     */
    public function parse($text, $args)
    {
        $i = 0;
        $me = $this;
        $text = preg_replace_callback('|#\?|', function () use (&$i, $args, $me) {
            return $me->quoteName($args[$i++]);
        }, $text);
        return preg_replace_callback('|\?|', function () use (&$i, $args, $me) {
            return $me->quote($args[$i++]);
        }, $text);
    }

    /**
     * Connect to database
     * @param array $cfg Configuration object (host, user, password, dbname)
     * @return AirMySqlQuery
     * @throws Exception
     */
    public function connect($cfg = null)
    {
        if ($cfg) {
            $this->cfg = $cfg;
        }

        if ($this->connection) {
            return $this;
        }

        $this->connection = new mysqli(
            $this->cfg->host,
            $this->cfg->user,
            $this->cfg->pass,
            $this->cfg->name
        );

        if (!$this->connection || $this->connection->connect_errno) {
            throw new Exception('Connection failed: ' . mysqli_connect_error());
        }

        $charset = isset($this->cfg->charset) ? $this->cfg->charset : 'utf8';
        $this->connection->set_charset($charset);
        return $this;
    }

    /**
     * Create new query with same connection
     * @return AirMySqlQuery
     */
    public function __invoke()
    {
        $this2 = new AirMySqlQuery($this);
        return $this2;
    }

    /**
     * Disconnect from database
     */
    public function disconnect()
    {
        if ($this->connection) $this->connection->close();
    }

    /**
     * Select all query
     * @return AirMySqlQuery
     */
    public function selectFrom()
    {
        $args = func_get_args();
        $args[0] = 'SELECT * FROM ' . $args[0];
        $this->queryArgs($args);
        return $this;
    }

    /**
     * Create SQL IN(...) operator
     * @param $array
     * @return AirMySqlQuery
     */
    public function in($array)
    {
        $this->query(sprintf(' IN(%s) ', $this->quoteIn($array)));
        return $this;
    }

    /**
     * Quote array for IN(?)
     * @param array $array
     * @return string
     */
    public function quoteIn($array)
    {
        $result = array();
        foreach ($array as $val) {
            $result[] = $this->quote($val);
        }

        return join(', ', $result);
    }

    /**
     * Quote database value
     * @param mixed $val
     * @return string
     */
    public function quote($val)
    {
        if ($val === null) return 'NULL';
        if ($val === false) return '0';
        if ($val === true) return '1';
        if (is_int($val)) return (string)$val;
        if (is_array($val) || is_object($val)) {
            $values = array_map(array($this, 'quote'), (array)$val);
            return implode(', ', $values);
        }
        return "'" . $this->connection->real_escape_string($val) . "'";
    }

    /**
     * Create SQL NOT IN(...) operator
     * @param $array
     * @return AirMySqlQuery
     */
    public function notIn($array)
    {
        $this->query(sprintf(' NOT IN(%s) ', $this->quoteIn($array)));
        return $this;
    }

    /**
     * Limit query
     * @param int $limit
     * @return AirMySqlQuery
     */
    public function limit($limit)
    {
        $args = func_get_args();
        $this->query('LIMIT ' . (int)$limit . (isset($args[1]) ? (', ' . (int)$args[1]) : ''));
        return $this;
    }

    /**
     * Begin new transaction
     * @return AirMySqlQuery
     */
    public function begin()
    {
        return $this->query('BEGIN')->exec();
    }

    /**
     * Commit transaction
     * @return AirMySqlQuery
     */
    public function commit()
    {
        return $this->query('COMMIT')->exec();
    }

    /**
     * Rollback transaction
     * @return AirMySqlQuery
     */
    public function rollback()
    {
        return $this->query('ROLLBACK')->exec();
    }

    /**
     * Insert data from assoc array of object
     * @param string $table
     * @param array $data
     * @return AirMySqlQuery
     */
    public function insertArray($table, $data)
    {
        $this->insertInto($table);
        $q = array();
        foreach ($data as $k => $v) {
            $q[] = $this->quoteName($k) . '=' . $this->quote($v);
        }
        return $this->set(implode(', ', $q));
    }

    public function insertInto($table)
    {
        $args = func_get_args();
        $args[0] = ' INSERT INTO ' . $table;
        $this->queryArgs($args);
        return $this;
    }

    public function deleteFrom($table)
    {
        $args = func_get_args();
        $args[0] = 'DELETE FROM ' . $table;
        $this->queryArgs($args);
        return $this;
    }

    public function orderBy($condition)
    {
        $this->query('ORDER BY ' . $condition);
        return $this;
    }

    /**
     * Insert bulk data
     * @param string $table
     * @param array $data
     * @return AirMySqlQuery
     * @throws Exception
     *
     */
    public function insertBulk($table, $data)
    {
        $data = (array)$data;
        if (!isset($data[0])) {
            throw new Exception("Array should contain at least 1 element");
        }

        $this->insertInto($table);

        $fields = array();

        foreach (array_keys((array)$data[0]) as $field) {
            $fields[] = $this->quoteName($field);
        }

        $this->query(' (' . join(',', $fields) . ') VALUES ');

        $blocks = array();
        foreach ($data as $row) {
            $values = array();
            foreach ($row as $value) {
                $values[] = $this->quote($value);
            }
            $blocks[] = "(" . join(',', $values) . ")";
        }

        $this->query(join(",", $blocks));
        return $this;
    }

    /**
     * Replace bulk data
     * @param string $table
     * @param array $data
     * @return AirMySqlQuery
     * @throws Exception
     */
    public function replaceBulk($table, $data)
    {
        if (!isset($data[0])) {
            throw new Exception("Array should contain at least 1 element");
        }

        $this->replaceInto($table);

        $fields = array();

        foreach (array_keys((array)$data[0]) as $field) {
            $fields[] = $this->quoteName($field);
        }

        $this->query(' (' . join(',', $fields) . ') VALUES ');

        $blocks = array();
        foreach ($data as $row) {
            $values = array();
            foreach ($row as $value) {
                $values[] = $this->quote($value);
            }
            $blocks[] = "(" . join(',', $values) . ")";
        }

        $this->query(join(",", $blocks));
        return $this;
    }

    /**
     * Quote database name
     * @param string $name
     * @return string
     */
    public function quoteName($name)
    {
        if (is_array($name) || is_object($name)) {
            $names = array_map(array($this, 'quoteName'), (array)$name);
            return implode(', ', $names);
        }
        return '`' . $name . '`';
    }

    /**
     * Get sql text
     * @return string
     */
    public function asSql()
    {
        $sql = str_replace("\n", " ", $this->sql);
        return preg_replace('/(\s+)/', " ", $sql);
    }

    /**
     * Magically append query with method name and text, replace placeholders
     * @param string $name
     * @param array $args
     * @return AirMySqlQuery
     */
    public function __call($name, $args)
    {
        $line = $name;
        $offset = 0;
        $words = array();
        while (preg_match('/([A-Za-z][a-z]*)(_*)/', $line, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $words [] = strtoupper($m[1][0]);
            $offset = $m[0][1] + strlen($m[0][0]);
        }
        $args[0] = implode(' ', $words) . (isset($args[0]) ? (' ' . $args[0]) : '');
        return $this->queryArgs($args);
    }

    /**
     * Fetch row as object
     * @param string $class Result type
     * @return object
     */
    public function fetchRow($class = null)
    {
        return call_user_func_array(array($this, 'fetchObject'), func_get_args());
    }

    /**
     * Fetch all rows as objects
     * @param string $class Result objects type
     * @return array
     */
    public function fetchAll($class = null)
    {
        return call_user_func_array(array($this, 'fetchAllObject'), func_get_args());
    }

    /**
     * Append query text and replace placeholders
     * @param string $text
     * @return AirMySqlQuery
     */
    public function q($text)
    {
        return $this->queryArgs(func_get_args());
    }

    // Abstract methods

    /**
     * Append query if condition is positive
     * @param mixed $condition
     * @param string $text
     * @return AirMySqlQuery
     */
    public function ifQ($condition, $text)
    {
        return call_user_func_array(array($this, 'ifQuery'), func_get_args());
    }

    /**
     * Append query text from array of arguments
     * @param array $args
     * @return AirMySqlQuery
     */
    public function queryArgs(array $args)
    {
        if (count($args) > 1) {
            $this->sql .= $this->parse($args[0], array_slice($args, 1)) . "\n";
        } else {
            $this->sql .= $args[0] . "\n";
        }
        return $this;
    }

    /**
     * Append query if condition is positive
     * @param mixed $condition
     * @param string $text
     * @return AirMySqlQuery
     */
    public function ifQuery($condition, $text)
    {
        if ($condition) {
            $args = array_slice(func_get_args(), 1);
            return $this->queryArgs($args);
        }
        return $this;
    }


    /**
     * Insert data from assoc array of object
     * @param string $table
     * @param array $data
     * @return AirMySqlQuery
     */
    public function replaceArray($table, $data)
    {
        $this->replace($table);
        $q = array();
        foreach ($data as $k => $v) {
            $q[] = $this->quoteName($k) . '=' . $this->quote($v);
        }
        return $this->set(implode(', ', $q));
    }

    /**
     * Gnereate set statement from an array of values
     * @param array $values
     * @return AirMySqlQuery
     */
    public function setValues($values)
    {
        $q = array();
        foreach ($values as $k => $v) {
            $q[] = $this->quoteName($k) . '=' . $this->quote($v);
        }
        return $this->set(implode(', ', $q));
    }


    /**
     * Fetch single value from database
     * @return mixed
     */
    public function fetchCell()
    {
        $this->exec();
        $row = $this->result->fetch_row();
        return $row[0];
    }

    /**
     * Execute query
     * @param bool $last_id
     * @return AirMySqlQuery
     * @throws Exception
     */
    public function exec($last_id = false)
    {
        $sql = $this->sql;
        $this->result = @$this->connection->query($sql);

        if (!$this->result && substr_count($this->connection->error, 'gone away')) {
            $this->connection = null;
            $this->connect();
            $this->exec($last_id);
        }

        $this->sql = '';
        $this->classname = null;
        if (!$this->result) {
            throw new Exception(sprintf("MySQL ERROR: %s, SQL: %s", $this->connection->error, $sql),
                $this->connection->errno);
        }
        return $last_id ? $this->lastId() : $this->result;
    }

    /**
     * Get last inserted id
     * @return mixed
     */
    public function lastId()
    {
        return $this->connection->insert_id;
    }

    /**
     * Select query
     * @param string $what
     * @return AirMySqlQuery
     */
    public function select($what = '*')
    {
        $args = func_get_args();
        $args[0] = 'SELECT ' . $what;
        $this->queryArgs($args);
        return $this;
    }

    /**
     * @param $what
     * @return $this
     */
    public function set($what)
    {
        $args = func_get_args();
        $args[0] = ' SET ' . $what;
        $this->queryArgs($args);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function replace($table)
    {
        $args = func_get_args();
        $args[0] = 'REPLACE ' . $table;
        $this->queryArgs($args);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function update($table)
    {
        $args = func_get_args();
        $args[0] = 'UPDATE ' . $table;
        $this->queryArgs($args);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function replaceInto($table)
    {
        $args = func_get_args();
        $args[0] = 'REPLACE INTO ' . $table;
        $this->queryArgs($args);
        return $this;
    }


    public function getLock($name, $timeout = 0.1)
    {
        return (bool)$this->select('GET_LOCK(?, ?)', $name, $timeout)->fetchCell();
    }

    public function releaseLock($name)
    {
        return (bool)$this->select('RELEASE_LOCK(?)', $name)->fetchCell();
    }

    /**
     * Get affected rows
     * @return int
     */
    public function affectedRows()
    {
        return $this->connection->affected_rows;
    }

    /**
     * Fetch single value from database
     * @return mixed
     */
    public function fetchOne()
    {
        return $this->fetchCell();
    }

    /**
     * Fetch all rows as array of objects
     * @param string $class Result type
     * @return array
     */
    public function fetchAllObject($class = null)
    {
        if (!$class) $class = $this->classname;
        $this->exec();
        $res = array();
        while ($row = $class ? $this->result->fetch_object($class)
            : $this->result->fetch_object()) {
            $res [] = $row;
        }
        return $res;
    }

    /**
     * Fetch all rows as array of associative arrays
     * @return array
     */
    public function fetchAllAssoc()
    {
        $this->exec();
        $res = array();
        while ($row = $this->result->fetch_assoc()) {
            $res [] = $row;
        };
        return $res;
    }

    /**
     * Fetch first result field from all rows as array
     * @return array
     */
    public function fetchColumn()
    {
        return array_map(function ($row) {
            return $row[0];
        }, $this->fetchAllArray());
    }

    /**
     * Fetch all rows as array of arrays
     * @return array
     */
    public function fetchAllArray()
    {
        $this->exec();
        $res = array();
        while ($row = $this->result->fetch_array()) {
            $res [] = $row;
        };
        return $res;
    }

    /**
     * Fetch row as object from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @param string $class Result type
     * @return object
     */
    public function getObject($table, $field, $value, $class = null)
    {
        $this->select()->from($table)->where("$field = ?", $value);
        return $this->fetchObject($class);
    }

    /**
     * Create WHERE operator
     * @param string $condition
     * @return AirMySqlQuery
     */
    public function where($condition)
    {
        $args = func_get_args();
        $args[0] = ' WHERE ' . $condition;
        $this->queryArgs($args);
        return $this;
    }

    /**
     * Create FROM operator
     * @param string $table
     * @return AirMySqlQuery
     */
    public function from($table)
    {
        $args = func_get_args();
        $args[0] = ' FROM ' . $table;
        $this->queryArgs($args);
        return $this;
    }

    /**
     * Fetch row as object
     * @param string $class Result type
     * @return object
     */
    public function fetchObject($class = null)
    {
        if (!$class) $class = $this->classname;
        $this->exec();
        $row = $class ? $this->result->fetch_object($class)
            : $this->result->fetch_object();
        return $row;
    }

    /**
     * Fetch row as array from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @return array
     */
    public function getArray($table, $field, $value)
    {
        $this->select()->from($table)->where("{$field} = ?", $value);
        return $this->fetchArray();
    }

    // Useless methods

    /**
     * Fetch row as array
     * @return array
     */
    public function fetchArray()
    {
        $this->exec();
        $row = $this->result->fetch_row();
        return $row;
    }

    /**
     * Fetch row as associative array from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @return array
     */
    public function getAssoc($table, $field, $value)
    {
        $this->select()->from($table)->where("{$field} = ?", $value);
        return $this->fetchAssoc();
    }

    /**
     * Fetch row as associative array
     * @return array
     */
    public function fetchAssoc()
    {
        $this->exec();
        $row = $this->result->fetch_assoc();
        return $row;
    }

    /**
     * Get found rows
     * @return mixed
     */
    public function foundRows()
    {
        return (integer)$this->select('FOUND_ROWS()')->fetchCell();
    }

    /**
     * Print current query
     * @return $this
     */
    public function printSql()
    {
        echo $this->asSql();
        return $this;
    }

}