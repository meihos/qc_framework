<?php

define('PHP_REQUIRE_VERSION_ID', 50400);

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

if (PHP_VERSION_ID < PHP_REQUIRE_VERSION_ID) {
    $mainVersion = intval(PHP_REQUIRE_VERSION_ID / 10000);
    $subVersion = intval((PHP_REQUIRE_VERSION_ID - $mainVersion * 10000) / 100);

    die('Update PHP to version ' . $mainVersion . '.' . $subVersion . ' or highest');
}

/**
 * @param $level
 * @param $message
 * @param $file
 * @param $line
 * @param $context
 * @throws Exception
 */
function QC_Custom_Error($level, $message, $file, $line, $context)
{
    $messageException = 'Error on line #' . $line . ' at file [' . $file . '] with message - ' . $message . '[' . $level . ']';
    throw new Exception($messageException);
}

set_error_handler("QC_Custom_Error", E_ALL);


/**
 * @param $rootDir
 * @param $initFolders
 * @return \Core\Core|null
 */
function QC_Init_Core($rootDir, $initFolders)
{
    define('PATH_TO_SYSTEM', $rootDir);

    if ((!is_array($initFolders)) || (!isset($initFolders['Core']))) {
        die('Wrong init folder for [Core]');
    }

    require_once __DIR__ . '/Loader.php';
    $classLoader = new Core\AutoLoad\Loader();

    $classLoader->addPrefixes($initFolders);
    $classLoader->setUseIncludePath(true);
    $classLoader->register(false);


    if ((isset($initFolders['Vendor'])) && file_exists($initFolders['Vendor'] . '/autoload.php')) {
        require_once $initFolders['Vendor'] . '/autoload.php';
    }
    $core = null;

    try {
        $core = new Core\Core($classLoader);
    } catch (Exception $e) {
        echo $e->getMessage();
        echo $e->getTraceAsString();
    }


    return $core;
}



