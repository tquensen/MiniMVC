<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['MODLC.defaultIndex'] = array(
    'route' => 'MODLC(.:_format:)', // .format is optional (in brackets)
    'controller' => 'MODULE_Default',
    'action' => 'index',
    'parameter' => array('_format' => 'html'), //set html as default format
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    'method' => 'GET',
    'rights' => 0 //$rights->getRights('user')
);

$MiniMVC_routes['MODLC.defaultCreate'] = array(
    'route' => 'MODLC/create',
    'controller' => 'MODULE_Default',
    'action' => 'create',
    'method' => array('GET', 'POST'),
    'parameter' => array(),
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRights('publish')
);
$MiniMVC_routes['MODLC.defaultShow'] = array(
    'route' => 'MODLC/:id:(.:_format:)', // .format is optional (in brackets)
    'controller' => 'MODULE_Default',
    'action' => 'show',
    'method' => 'GET',
    //'model' => array('MODULE', 'id'), // array(modelname, property, parameter) or array('model1' => array(modelname, property, parameter),'modelx' => array(modelname, property, parameter))
                                        //automatically load a model with the name modelname by the field 'property' (defaults to the models identifier) with the value provided py routeparameter :parameter: (defaults to the property)
                                        // returns null if not found // in your controller, you can access it with $params['model'] (or $params['model']['model1'], $params['model']['modelx'] if multiple models were defined)
    'parameter' => array('id' => false, '_format' => 'html'),
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    'rights' => 0 //$rights->getRights('user')
);
$MiniMVC_routes['MODLC.defaultEdit'] = array(
    'route' => 'MODLC/:id:/edit',
    'controller' => 'MODULE_Default',
    'action' => 'edit',
    'method' => array('GET', 'POST'),
    //'model' => array('MODULE', 'id'),
    'parameter' => array('id' => false),
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRights('publish')
);
$MiniMVC_routes['MODLC.defaultDelete'] = array(
    'route' => 'MODLC/:id:/delete',
    'controller' => 'MODULE_Default',
    'action' => 'delete',
    'method' => 'DELETE',
    //'model' => array('MODULE', 'id'),
    'parameter' => array('id' => false),
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRights('publish')
);


$MiniMVC_routes['MODLC.defaultFallback'] = array(
    'route' => 'MODLC(/:_controller:)(/:_action:)(.:_format:)',
    'parameter' => array('_module' => 'MODULE', '_controller' => 'Default', '_action' => 'index', '_format' => 'html'),
    'parameterPatterns' => array('_controller' => '[a-zA-Z]+', '_action' => '[a-zA-Z]+', '_format' => 'json|xml'), //allow xml and json as alternative formats
    'method' => 'GET',
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRights('publish')
);