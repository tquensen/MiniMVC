<?php
error_reporting(E_ALL | E_STRICT);
session_start();
define('BASEPATH', dirname(__FILE__).'/../');
define('APPPATH', BASEPATH . 'app/');
define('MODULEPATH', BASEPATH . 'module/');
define('WEBPATH', BASEPATH . 'web/');
define('DATAPATH', BASEPATH . 'data/');
define('CACHEPATH', BASEPATH . 'cache/');
include BASEPATH.'lib/MiniMVC/Autoload.php';
include BASEPATH.'lib/MiniMVC/Registry.php';
include BASEPATH.'lib/MiniMVC/Settings.php';
spl_autoload_register(array('MiniMVC_Autoload', 'autoload'));

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('frontend', 'dev', false);

try {
    echo MiniMVC_Registry::getInstance()->dispatcher->dispatch();
} catch (Exception $e) {
    include BASEPATH.'web/error/500.debug.php';
}
