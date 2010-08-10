#!/usr/bin/php
<?php
error_reporting(E_ALL | E_STRICT);

if (!isset($_SERVER['argv']) || count($_SERVER['argv']) < 2) {
    exit;
}

define('BASEPATH', dirname(__FILE__).'/');
define('APPPATH', BASEPATH . 'app/');
define('MODULEPATH', BASEPATH . 'module/');
define('WEBPATH', BASEPATH . 'web/');
define('DATAPATH', BASEPATH . 'data/');
define('CACHEPATH', BASEPATH . 'cache/');
include BASEPATH.'lib/MiniMVC/Autoload.php';
include BASEPATH.'lib/MiniMVC/Registry.php';
include BASEPATH.'lib/MiniMVC/Settings.php';
spl_autoload_register(array('MiniMVC_Autoload', 'autoload'));

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('', 'dev', true);

echo MiniMVC_Registry::getInstance()->task->dispatch($_SERVER['argv']) . "\n";
