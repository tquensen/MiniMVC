<?php

$MiniMVC_db['mongo'] = array( //for server and options, @see http://php.net/manual/en/mongo.construct.php
	'server' => 'mongodb://localhost:27017', //optional, defaults to "localhost:27017" (or whatever was specified in php.ini for mongo.default_host and mongo.default_port)
	'database' => 'mongo', //optional, if no database is given, the connection name will be used ("mongo" in this case)
	'options' => array('persist' => 'minimvc'), //optional, is only used when server is specified. Don't use "connect"=>false, as the connection class automatically selects the db after initialisation
);
 