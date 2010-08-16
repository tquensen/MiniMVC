<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '0');
session_start();
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