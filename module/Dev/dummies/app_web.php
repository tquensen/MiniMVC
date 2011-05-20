<?php
include dirname(__FILE__).'/../bootstrap.php';
include MINIMVCPATH.'MiniMVC/FileCache.php';
//include MINIMVCPATH.'MiniMVC/ApcCache.php'; //recommended, if APC is available

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('APP', 'prod', new MiniMVC_FileCache());

try {
    echo MiniMVC_Registry::getInstance()->dispatcher->dispatch()->parse();
} catch (Exception $e) {
    try {
        echo MiniMVC_Registry::getInstance()->dispatcher->getErrorPage($e)->parse();
    } catch (Exception $e) {
        include BASEPATH.'web/error/500.php';
    }
}