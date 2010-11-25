<?php
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
{
    die('Error 403 Forbidden!');
}

include dirname(__FILE__).'/../bootstrap.php';
include MINIMVCPATH.'MiniMVC/DummyCache.php';

ini_set('display_errors', '1');

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('frontend', 'dev', new MiniMVC_DummyCache());

try {
    echo MiniMVC_Registry::getInstance()->dispatcher->dispatch();
} catch (Exception $e) {
    include BASEPATH.'web/error/500.debug.php';
}
