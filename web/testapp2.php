<?php
include dirname(__FILE__).'/../bootstrap.php';

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('testapp2', 'prod', true);

try {
    echo MiniMVC_Registry::getInstance()->dispatcher->dispatch();
} catch (Exception $e) {
    include BASEPATH.'web/error/500.php';
}