<?php
$MiniMVC_routes['core.error401'] = array(
    'route' => 'error401',
    'active' => false, //don't allow direct access
    'controller' => 'Core_Error',
    'action' => 'error401',
);
$MiniMVC_routes['core.error403'] = array(
    'route' => 'error403',
    'active' => false, //don't allow direct access
    'controller' => 'Core_Error',
    'action' => 'error403',
);
$MiniMVC_routes['core.error404'] = array(
    'route' => 'error404',
    'active' => false, //don't allow direct access
    'controller' => 'Core_Error',
    'action' => 'error404',
);
$MiniMVC_routes['core.error404.debug'] = array(
    'route' => 'error404debug',
    'active' => false, //don't allow direct access
    'controller' => 'Core_Error',
    'action' => 'error404',
    'parameter' => array('debug' => true)
);
$MiniMVC_routes['core.error500'] = array(
    'route' => 'error500',
    'active' => false, //don't allow direct access
    'controller' => 'Core_Error',
    'action' => 'error500',
);
$MiniMVC_routes['core.error500.debug'] = array(
    'route' => 'error404debug',
    'active' => false, //don't allow direct access
    'controller' => 'Core_Error',
    'action' => 'error500',
    'parameter' => array('debug' => true)
);
