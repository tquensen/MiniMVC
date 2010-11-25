<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '0');
session_start();
define('BASEPATH', dirname(__FILE__).'/');
define('MINIMVCPATH', BASEPATH . 'lib/MiniMVC/');
define('APPPATH', BASEPATH . 'app/');
define('MODULEPATH', BASEPATH . 'module/');
define('WEBPATH', BASEPATH . 'web/');
define('DATAPATH', BASEPATH . 'data/');
define('CACHEPATH', BASEPATH . 'cache/');
include MINIMVCPATH.'MiniMVC/Autoload.php';
include MINIMVCPATH.'MiniMVC/Registry.php';
include MINIMVCPATH.'MiniMVC/Settings.php';
include MINIMVCPATH.'MiniMVC/Cache.php';
spl_autoload_register(array('MiniMVC_Autoload', 'autoload'));