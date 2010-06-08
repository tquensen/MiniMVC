<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '0');
session_start();
define('BASEPATH', str_replace('//', '/', dirname(__FILE__).'/'));
include BASEPATH.'MiniMVC/Autoload.php';
spl_autoload_register(array('MiniMVC_Autoload', 'autoload'));

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('prod', true);

try {
    echo MiniMVC_Registry::getInstance()->dispatcher->dispatch();
} catch (Exception $e) {
    include BASEPATH.'Web/error/500.php';
}