<?php
$MiniMVC_routes['MODLC.CONTROLLERLCFIRSTIndex'] = array(
    'route' => 'MODLC(.:_format:)', // .format is optional (in brackets)
    'controller' => 'MODULE_CONTROLLER',
    'action' => 'index',
    'parameter' => array('_format' => 'html'), //set html as default format
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    //'ajaxLayout' => array('html' => false), //disable the layout when requesting the html view via XMLHttpRequest
    'method' => 'GET',
    //'rights' => false // use false for no restrictions,
                        // a right as string (e.g. 'user') th require that right,
                        // an array of rights to require ALL of them (AND)
                        //   e.g. array('user', 'publish') = user AND publish
                        // a 2 dimensional array to require at least one right (OR)
                        //   e.g. array(array('user', 'guest')) = user OR guest
                        // a combination of both (each new level switches logic between AND and OR)
                        //   e.g. array('user', array('publish', 'administrate')) = user AND (publish OR administrate) = (user AND publish) OR (user AND administrate)
                        //        array(array('administrate', array('moderate', 'publish'))) = administrate OR (moderate AND publish)
);

$MiniMVC_routes['MODLC.CONTROLLERLCFIRSTNew'] = array(
    'route' => 'MODLC/new(.:_format:)',
    'controller' => 'MODULE_CONTROLLER',
    'action' => 'new',
    'method' => 'GET',
    'parameter' => array('_format' => 'html'), //set html as default format
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    'active' => false, //this route must be activated for each app to work
    //'rights' => 'publish'
);
$MiniMVC_routes['MODLC.CONTROLLERLCFIRSTCreate'] = array(
    'route' => 'MODLC(.:_format:)',
    'controller' => 'MODULE_CONTROLLER',
    'action' => 'create',
    'method' => 'POST',
    'parameter' => array('_format' => 'html'), //set html as default format
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    'active' => false, //this route must be activated for each app to work
    //'rights' => 'publish'
);

$MiniMVC_routes['MODLC.CONTROLLERLCFIRSTShow'] = array(
    'route' => 'MODLC/:id:(.:_format:)', // .format is optional (in brackets)
    'controller' => 'MODULE_CONTROLLER',
    'action' => 'show',
    'method' => 'GET',
    'model' => array('MODULE', 'id'), // array(modelname, property, parameter) or array('model1' => array(modelname, property, parameter),'modelx' => array(modelname, property, parameter))
                                        //automatically load a model with the name modelname by the field 'property' (defaults to the models identifier) with the value provided py routeparameter :parameter: (defaults to the property)
                                        // returns null if not found // in your controller, you can access it with $params['model'] (or $params['model']['model1'], $params['model']['modelx'] if multiple models were defined)
    'parameter' => array('id' => false, '_format' => 'html'),
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    //'rights' => false
);
$MiniMVC_routes['MODLC.CONTROLLERLCFIRSTEdit'] = array(
    'route' => 'MODLC/:id:/edit(.:_format:)',
    'controller' => 'MODULE_CONTROLLER',
    'action' => 'edit',
    'method' => 'GET',
    'model' => array('MODULE', 'id'),
    'parameter' => array('id' => false, '_format' => 'html'),
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    'active' => false, //this route must be activated for each app to work
    //'rights' => 'publish'
);
$MiniMVC_routes['MODLC.CONTROLLERLCFIRSTUpdate'] = array(
    'route' => 'MODLC/:id:(.:_format:)',
    'controller' => 'MODULE_CONTROLLER',
    'action' => 'update',
    'method' => 'POST',
    'model' => array('MODULE', 'id'),
    'parameter' => array('id' => false, '_format' => 'html'),
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    'active' => false, //this route must be activated for each app to work
    //'rights' => 'publish'
);

$MiniMVC_routes['MODLC.CONTROLLERLCFIRSTDelete'] = array(
    'route' => 'MODLC/:id:/delete(.:_format:)',
    'controller' => 'MODULE_CONTROLLER',
    'action' => 'delete',
    'method' => 'DELETE',
    'model' => array('MODULE', 'id'),
    'parameter' => array('id' => false, '_format' => 'html'),
    //'parameterPatterns' => array('_format' => 'json|xml'), //allow xml and json as alternative formats
    'active' => false, //this route must be activated for each app to work
    //'rights' => 'publish'
);

/*
$MiniMVC_routes['MODLC.CONTROLLERLCFIRSTFallback'] = array(
    'route' => 'MODLC(_:_controller:)(/:_action:)(.:_format:)',
    'parameter' => array('_module' => 'MODULE', '_controller' => 'CONTROLLER', '_action' => 'index', '_format' => 'html'),
    'parameterPatterns' => array('_controller' => '[A-Z][a-zA-Z]*', '_action' => '[a-zA-Z]+', '_format' => 'json|xml'), //allow xml and json as alternative formats
    'method' => 'GET',
    'active' => false, // you shoud use this route only for testing
    //'rights' => 'publish'
);
 */