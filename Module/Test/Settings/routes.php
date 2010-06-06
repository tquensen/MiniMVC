<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['test.defaultIndex'] = array(
    'route' => 'test/index',
    'controller' => 'Test_Default',
    'action' => 'index',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);
$MiniMVC_routes['test.defaultShow'] = array(
    'route' => 'test/:id:',
    'controller' => 'Test_Default',
    'action' => 'show',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);