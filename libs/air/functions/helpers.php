<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Helper functions
 */

/**
 * Get request value and trim
 * @param string $param
 * @param mixed $default
 * @return mixed
 */
function r($param, $default = '')
{
    if (isset($_REQUEST[$param]))
        return is_array($_REQUEST[$param]) ? $_REQUEST[$param] : trim($_REQUEST[$param]);
    return $default;
}


/**
 * Return date in database format (Y-m-d)
 * @param mixed $time Integer time or string date or 0 (for current date)
 * @return string
 */
function dbdate($time = false)
{
    if (is_string($time)) {
        $time = strtotime($time);
    }
    return $time ? date('Y-m-d', $time) : date('Y-m-d');
}


/**
 * Return time in database format (Y-m-d H:i:s)
 * @param mixed $time Integer time or string date or 0 (for current time)
 * @return string
 */
function dbtime($time = false)
{
    if (is_string($time)) {
        $time = strtotime($time);
    }
    return $time ? date('Y-m-d H:i:s', $time) : date('Y-m-d H:i:s');
}


/**
 * @param $path
 * @return string
 */
function app_path($path)
{
    return ROOT . '/app/' . $path;
}

/**
 * @param $path
 * @return string
 */
function storage_path($path)
{
    return ROOT . '/storage/' . $path;
}

/**
 * @param $path
 * @return string
 */
function resource_path($path)
{
    return ROOT . '/resources/' . $path;
}

/**
 * @param $path
 * @return string
 */
function assets_path($path)
{
    return ROOT . '/assets/' . $path;
}

/**
 * @param $path
 * @return string
 */
function view_path($path)
{
    return ROOT . '/app/views/' . $path;
}

/**
 * Get associative array of objects
 * @param array $array Source array
 * @param string $field Field to use as key
 * @return array Result array
 */
function associate($array, $field)
{
    $res = array();
    foreach ($array as $r) {
        if (!empty($r->$field)) {
            $res[$r->$field] = $r;
        }
    }
    return $res;
}

/**
 * Get array of fields from array of objects
 * @param array $array
 * @param string $field
 * @return array
 */
function column($array, $field)
{
    $res = array();
    foreach ($array as $r) {
        if (!empty($r->$field)) {
            $res [] = $r->$field;
        }
    }
    return $res;
}

/**
 * @param string $instance
 * @return AirMySqlQuery
 * @throws Exception
 */
function db($instance)
{
    static $db = [];
    $cfg = cfg()->db;

    if (!isset($db[$instance])) {


        if (!isset($cfg->{$instance})) {
            throw new Exception('Database config not found: ' . $instance);
        }

        $db[$instance] = new AirMySqlQuery($cfg->{$instance});
    }

    return $db[$instance]();
}

/**
 * Get fully qualified URL to the given path
 * @param $path
 * @param array $params optional url parameters
 * @return string
 */
function url($path, $params = [])
{
    $url = cfg()->baseurl . '/' . $path;

    if ($params) {
        $url .= "?" . http_build_query($params);
    }

    return $url;
}

/**
 * Delete directory recursively
 * @param $dir
 * @return bool Operation success
 */
function rmdir_r($dir)
{
    $dir = realpath($dir);
    if (!is_dir($dir) || is_link($dir)) return false;
    $files = glob("{$dir}/*");
    foreach ($files as $file) {
        if (is_dir($file)) {
            rmdir_r($file);
        } else {
            unlink($file);
        }
    }
    return rmdir($dir);
}

/**
 * Send CORS headers
 */
function cors_headers()
{
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
}

/**
 * Get configuration as object
 * @return stdClass
 */
function cfg()
{
    static $cfg = null;
    static $lastRefreshTime = 0;
    static $refreshTimeout = 600;

    $time = time();

    if ($cfg === null || (($lastRefreshTime + $refreshTimeout) < $time)) {

        $cfg = require ROOT . '/config.php';
        $instance = "local";

        if (file_exists($instanceFile = ROOT . "/instance.txt")) {
            $instance = trim(file_get_contents($instanceFile));
        }

        $localConfigPath = sprintf('%s/config.%s.php', ROOT, $instance);

        if (file_exists($localConfigPath)) {
            $localConfig = require $localConfigPath;
            $cfg = array_replace_recursive($cfg, $localConfig);
        }

        # simplest way to make an object from assoc array :)
        $cfg = json_decode(json_encode($cfg));
        $lastRefreshTime = $time;
    }

    return $cfg;
}

/**
 * Check if string is JSON
 * @param $json
 * @return bool
 */
function is_json($json)
{
    return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/',
        preg_replace('/"(\\.|[^"\\\\])*"/', '', $json));
}

/**
 * Escape SQL like special characters (escape character is \)
 * @param string $str
 * @return string
 */
function escape_like($str)
{
    return str_replace('_', '\\_', str_replace('%', '\\%', str_replace('\\', '\\\\', $str)));
}

/**
 * Render view with specified arguments
 * @param $filename string template name
 * @param $args array
 * @throws Exception
 * @return string
 */
function view($filename, $args = [])
{
    static $twig;

    if (!class_exists('Twig_Environment')) {
        throw new Exception("Twig_Environment class not found!");
    }

    if (!$twig) {
        $loader = new Twig_Loader_Filesystem(ROOT);
        $twig = new Twig_Environment($loader, [
            'cache' => cfg()->twig->cache ? storage_path("cache/twig") : false,
        ]);

        $twig->addFunction(new Twig_SimpleFunction('url', function ($path, $params = []) {
            return url($path, $params);
        }));
    }

    return $twig->render($filename, $args);
}