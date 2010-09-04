<?php
$MiniMVC_routes['core.error401'] = array(
    'route' => 'error401',
    'controller' => 'Core_Error',
    'action' => 'error401',
);
$MiniMVC_routes['core.error403'] = array(
    'route' => 'error403',
    'controller' => 'Core_Error',
    'action' => 'error403',
);
$MiniMVC_routes['core.error404'] = array(
    'route' => 'error404',
    'controller' => 'Core_Error',
    'action' => 'error404',
);
$MiniMVC_routes['core.error500'] = array(
    'route' => 'error500',
    'controller' => 'Core_Error',
    'action' => 'error500',
);
$MiniMVC_routes['core.error500.debug'] = array(
    'route' => 'error500',
    'controller' => 'Core_Error',
    'action' => 'error500',
    'parameter' => array('debug' => true)
);
