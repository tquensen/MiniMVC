#!/usr/bin/php
<?php
if (!isset($_SERVER['argv']) || count($_SERVER['argv']) < 2) {
    exit;
}

include dirname(__FILE__).'/bootstrap.php';
include MINIMVCPATH.'MiniMVC/DummyCache.php';

ini_set('display_errors', '1');

MiniMVC_Registry::getInstance()->settings = new MiniMVC_Settings('', '', new MiniMVC_DummyCache());

echo MiniMVC_Registry::getInstance()->task->dispatch($_SERVER['argv']) . "\n";
