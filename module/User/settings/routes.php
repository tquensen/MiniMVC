<?php

// routes for controller User
// ===================================

$MiniMVC_routes['user.userIndex'] = array(
    'route' => 'user(.:_format:)', // .format is optional (in brackets)
    'controller' => 'User_User',
    'action' => 'index',
    'parameter' => array('_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'method' => 'GET',
    'active' => true, //this route must be activated for each app to work
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

$MiniMVC_routes['user.userLogout'] = array(
    'route' => 'user/logout(.:_format:)',
    'controller' => 'User_User',
    'action' => 'logout',
    'method' => array('GET', 'POST'),
    'parameter' => array('_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'active' => true, //this route must be activated for each app to work
    'rights' => 'user'
);

$MiniMVC_routes['user.userLogin'] = array(
    'route' => 'user/login(.:_format:)',
    'controller' => 'User_User',
    'action' => 'login',
    'method' => array('GET', 'POST'),
    'parameter' => array('_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'active' => true, //this route must be activated for each app to work
    'rights' => 'guest'
);

$MiniMVC_routes['user.userNew'] = array(
    'route' => 'user/new(.:_format:)',
    'controller' => 'User_User',
    'action' => 'new',
    'method' => 'GET',
    'parameter' => array('_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'active' => true, //this route must be activated for each app to work
    'rights' => 'guest'
);
$MiniMVC_routes['user.userCreate'] = array(
    'route' => 'user/new(.:_format:)',
    'controller' => 'User_User',
    'action' => 'create',
    'method' => 'POST',
    'parameter' => array('_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'active' => true, //this route must be activated for each app to work
    'rights' => 'guest'
);

$MiniMVC_routes['user.userShow'] = array(
    'route' => 'user/:slug:(.:_format:)', // .format is optional (in brackets)
    'controller' => 'User_User',
    'action' => 'show',
    'method' => 'GET',
    'model' => array('User', 'slug'), // array(modelname, property, parameter) or array('model1' => array(modelname, property, parameter),'modelx' => array(modelname, property, parameter))
                                        //automatically load a model with the name modelname by the field 'property' (defaults to the models identifier) with the value provided py routeparameter :parameter: (defaults to the property)
                                        // returns null if not found // in your controller, you can access it with $params['model'] (or $params['model']['model1'], $params['model']['modelx'] if multiple models were defined)
    'parameter' => array('slug' => false, '_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    //'rights' => false
);
$MiniMVC_routes['user.userEdit'] = array(
    'route' => 'user/:slug:/edit(.:_format:)',
    'controller' => 'User_User',
    'action' => 'edit',
    'method' => 'GET',
    'model' => array('User', 'slug'),
    'parameter' => array('slug' => false, '_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'active' => true, //this route must be activated for each app to work
    'rights' => 'user'
);
$MiniMVC_routes['user.userUpdate'] = array(
    'route' => 'user/:slug:/edit(.:_format:)',
    'controller' => 'User_User',
    'action' => 'update',
    'method' => 'POST',
    'model' => array('User', 'slug'),
    'parameter' => array('slug' => false, '_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'active' => true, //this route must be activated for each app to work
    'rights' => 'user'
);

$MiniMVC_routes['user.userDelete'] = array(
    'route' => 'user/:slug:(.:_format:)',
    'controller' => 'User_User',
    'action' => 'delete',
    'method' => 'DELETE',
    'model' => array('User', 'slug'),
    'parameter' => array('slug' => false, '_format' => 'default'), //set the default format
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'active' => true, //this route must be activated for each app to work
    'rights' => 'user'
);

?>
<?php

// fallback route
// ===================================

/*
$MiniMVC_routes['user.fallback'] = array(
    'route' => 'user(_:_controller:)(/:_action:)(.:_format:)',
    'parameter' => array('_module' => 'User', '_controller' => 'User', '_action' => 'index', '_format' => 'default'),
    'parameterPatterns' => array('_controller' => '[A-Z][a-zA-Z]*', '_action' => '[a-zA-Z]+', '_format' => 'html|json'), //allow html, json and/or other (like 'html|json|xml|atom') as alternative formats
    'method' => 'GET',
    'active' => false, // you shoud use this route only for testing
    //'rights' => 'publish'
);
 */