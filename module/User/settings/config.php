<?php
$MiniMVC_config['autoloadPaths'][] = MODULEPATH.'User/lib';
$MiniMVC_config['user']['loginRedirect'] = array(
    'route' => 'home',
    'parameter' => array(),
    'app' => null
);
$MiniMVC_config['user']['logoutRedirect'] = array(
    'route' => 'home',
    'parameter' => array(),
    'app' => null
);
