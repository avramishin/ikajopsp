<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Single bootstrap file, everything is included here
 */
error_reporting(E_ALL);
define('ROOT', __DIR__);

/**
 * Get composer shit
 */
$composer = ROOT . '/vendor/autoload.php';
if (file_exists($composer)) {
    require_once $composer;
}

/**
 * Helper functions to make life easier
 */
require_once ROOT . "/libs/air/functions/helpers.php";

/**
 * Magic autoload
 */
spl_autoload_register(function ($className) {

    $classPath = [
        ROOT . "/app/models/{$className}.php",
        ROOT . "/libs/air/classes/{$className}.php"
    ];

    if (preg_match('/^([A-Z][a-z]*)\w+/', $className, $m)) {
        $prefix = strtolower($m[1]);
        $classPath [] = ROOT . "/app/models/{$prefix}/{$className}.php";
    }

    foreach ($classPath as $class) {
        if (file_exists($class)) {
            require_once $class;
            return;
        }
    }
});


date_default_timezone_set(cfg()->timezone);
set_time_limit(0);

require_once ROOT . "/libs/errorhook/Listener.php";
$errorsToRss = new Debug_ErrorHook_Listener();
$errorsToRss->addNotifier(
    new Debug_ErrorHook_RemoveDupsWrapper(
        new Debug_ErrorHook_RssNotifier(
            Debug_ErrorHook_TextNotifier::LOG_ALL
        ),
        storage_path('errors'), // lock directory
        300 // do not resend the same error within 300 seconds
    )
);