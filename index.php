<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Root entry point for all HTTP requests
 */
require_once __DIR__ . '/bootstrap.php';

try {

    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (cfg()->subDir) {
        $url = str_replace(cfg()->subDir, '', $url);
    }

    $controllersDir = sprintf('%s/app/controllers', __DIR__);
    $controllers = array(
        sprintf('%s/%s.php', $controllersDir, $url),
        sprintf('%s/%sindex.php', $controllersDir, $url),
        sprintf('%s/%s/index.php', $controllersDir, $url)
    );

    $controllerFound = false;
    foreach ($controllers as $controller) {
        if (file_exists($controller)) {

            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: *");

            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                exit();
            }

            require $controller;
            $controllerFound = true;
            break;
        }
    }


    if (!$controllerFound) {
        throw new Exception(sprintf('%s not found', $url));
    }

} catch (Exception $e) {
    header("HTTP/1.0 404 Not Found");
    echo $e->getMessage();
}