<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['MODLC.defaultIndex'] = array(
    'route' => 'MODLC/index',
    'controller' => 'MODULE_Default',
    'action' => 'index',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);
$MiniMVC_routes['MODLC.defaultIndex.json'] = array(
    'route' => 'MODLC/index',
    'controller' => 'MODULE_Default',
    'action' => 'index',
    'format' => 'json',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);