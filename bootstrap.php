<?php
error_reporting(E_ALL | E_STRICT);

ini_set('display_errors', '0');
ini_set('log_errors', '1');

define('BASEPATH', dirname(__FILE__).'/');
define('MINIMVCPATH', BASEPATH . 'lib/MiniMVC/');
define('APPPATH', BASEPATH . 'app/');
define('MODULEPATH', BASEPATH . 'module/');
define('VIEWPATH', BASEPATH . 'view/');
define('WEBPATH', BASEPATH . 'web/');
define('DATAPATH', BASEPATH . 'data/');
define('CACHEPATH', BASEPATH . 'cache/');

include MINIMVCPATH.'MiniMVC/Autoload.php';
include MINIMVCPATH.'MiniMVC/Registry.php';
include MINIMVCPATH.'MiniMVC/Settings.php';
include MINIMVCPATH.'MiniMVC/Cache.php';

spl_autoload_register(array('MiniMVC_Autoload', 'autoload'));

ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '0'); //allow session per POST/GET, useful for API requests
ini_set('session.use_trans_sid', '0');

session_name('session_token');
session_start();
