#!/usr/bin/php
<?php
error_reporting(E_ALL | E_STRICT);

if (!isset($_SERVER['argv']) || count($_SERVER['argv']) < 2) {
    exit;
}

define('BASEPATH', str_replace('//', '/', dirname(__FILE__).'/'));
include BASEPATH.'Lib/MiniMVC/Autoload.php';
include BASEPATH.'Lib/MiniMVC/Registry.php';
include BASEPATH.'Lib/MiniMVC/Settings.php';
spl_autoload_register(array('MiniMVC_Autoload', 'autoload'));

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('dev', false);

echo MiniMVC_Registry::getInstance()->task->dispatch($_SERVER['argv']);
