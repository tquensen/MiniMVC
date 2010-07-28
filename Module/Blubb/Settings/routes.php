<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['blubb.defaultIndex'] = array(
    'route' => 'blubb/index(\\.:_format:)?',
    'controller' => 'Blubb_Default',
    'action' => 'index',
    'parameter' => array(),
    'parameterPatterns' => array('_format' => 'json'),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);
$MiniMVC_routes['blubb.defaultIndex.json'] = array(
    'route' => 'blubb/index',
    'controller' => 'Blubb_Default',
    'action' => 'index',
    'format' => 'json',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);