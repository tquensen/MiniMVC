<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['my.defaultIndex'] = array(
    'route' => 'my/index',
    'controller' => 'My_Default',
    'action' => 'index',
    'parameter' => array(),
    'rights' => false //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);


$MiniMVC_routes['my.defaultCreate'] = array(
    'route' => 'my/create',
    'controller' => 'My_Default',
    'action' => 'create',
    'parameter' => array(),
    'active' => false, //this route must be activated for each app to work
    'rights' => false //$rights->getRoleRights($rights->getRoleByKeyword('admin'))
);