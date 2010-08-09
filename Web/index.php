<?php
error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', '0');
session_start();
define('BASEPATH', dirname(__FILE__).'/../');
define('APPPATH', BASEPATH . 'App/');
define('MODULEPATH', BASEPATH . 'Module/');
define('WEBPATH', BASEPATH . 'Web/');
include BASEPATH.'Lib/MiniMVC/Autoload.php';
include BASEPATH.'Lib/MiniMVC/Registry.php';
include BASEPATH.'Lib/MiniMVC/Settings.php';
spl_autoload_register(array('MiniMVC_Autoload', 'autoload'));

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('Frontend', 'prod', true);

try {
    echo MiniMVC_Registry::getInstance()->dispatcher->dispatch();
} catch (Exception $e) {
    include BASEPATH.'Web/error/500.debug.php';
}