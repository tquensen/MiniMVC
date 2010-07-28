<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['d2test.defaultIndex'] = array(
    'route' => 'd2test/index',
    'controller' => 'D2Test_Default',
    'action' => 'index',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);

$MiniMVC_routes['d2test.add'] = array(
    'route' => 'd2test/add/:name:',
    'controller' => 'D2Test_Default',
    'action' => 'add',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);
