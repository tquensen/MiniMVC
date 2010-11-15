<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['MODLC.defaultIndex'] = array(
    'route' => 'MODLC',
    'controller' => 'MODULE_Default',
    'action' => 'index',
    'parameter' => array(),
    'rights' => 0 //$rights->getRights('user')
);
$MiniMVC_routes['MODLC.defaultIndex.json'] = array(
    'route' => 'MODLC/index.json',
    'controller' => 'MODULE_Default',
    'action' => 'index',
    'format' => 'json',
    'parameter' => array(),
    'rights' => 0 //$rights->getRights('user')
);
$MiniMVC_routes['MODLC.defaultCreate'] = array(
    'route' => 'MODLC/create',
    'controller' => 'MODULE_Default',
    'action' => 'create',
    'parameter' => array(),
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRights('publish')
);
$MiniMVC_routes['MODLC.defaultShow'] = array(
    'route' => 'MODLC/:id:',
    'controller' => 'MODULE_Default',
    'action' => 'show',
    //'model' => array('MODULE', 'id'), //automatically load a model with the name MODULE by the given field 'id' (or null if not found). in your controller, you can access it with $params['model']
    'parameter' => array('id' => false),
    'rights' => 0 //$rights->getRights('user')
);
$MiniMVC_routes['MODLC.defaultEdit'] = array(
    'route' => 'MODLC/:id:/edit',
    'controller' => 'MODULE_Default',
    'action' => 'edit',
    //'model' => array('MODULE', 'id'), //automatically load a model with the name MODULE by the given field 'id' (or null if not found). in your controller, you can access it with $params['model']
    'parameter' => array('id' => false),
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRights('publish')
);
$MiniMVC_routes['MODLC.defaultDelete'] = array(
    'route' => 'MODLC/:id:/delete',
    'controller' => 'MODULE_Default',
    'action' => 'delete',
    //'model' => array('MODULE', 'id'), //automatically load a model with the name MODULE by the given field 'id' (or null if not found). in your controller, you can access it with $params['model']
    'parameter' => array('id' => false),
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRights('publish')
);