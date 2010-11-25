<?php
include dirname(__FILE__).'/../bootstrap.php';
include MINIMVCPATH.'MiniMVC/FileCache.php';

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('frontend', 'prod', new MiniMVC_FileCache());

try {
    echo MiniMVC_Registry::getInstance()->dispatcher->dispatch();
} catch (Exception $e) {
    include BASEPATH.'web/error/500.php';
}