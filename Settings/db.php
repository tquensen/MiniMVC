<?php
$MiniMVC_db = array();

$MiniMVC_db['default'] = array(
	'host' => 'localhost',
	'username' => 'root',
	'password' => 'peniskopf',
	'database' => 'minimvc_mysqli'
);



$MiniMVC_db['doctrine'] = array(
	'dbtype' => 'mysql',
	'host' => 'localhost',
	'username' => 'root',
	'password' => 'peniskopf',
	'database' => 'minimvc',
	'prefix' => 'minimvc_',
);

$MiniMVC_db['doctrine2']['cacheClass'] = '\Doctrine\Common\Cache\ArrayCache';
$MiniMVC_db['doctrine2']['AutoGenerateProxyClasses'] = true;

$MiniMVC_db['doctrine2']['connection'] = $connectionParams = array(
    'dbname' => 'minimvc_doctrine2',
    'user' => 'root',
    'password' => 'peniskopf',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);