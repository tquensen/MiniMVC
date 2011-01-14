<?php
$MiniMVC_routes['my.index'] = array(
    'route' => 'page/index', 
    'controller' => 'My_Default',
    'action' => 'index',
    'method' => 'GET',
    'rights' => false
);

//route for static pages, see controller/Default.php -> staticAction
$MiniMVC_routes['my.static'] = array(
    'route' => 'page/:page:(.:_format:)', // .format is optional (in brackets)
    'controller' => 'My_Default',
    'action' => 'static',
    'parameter' => array('page' => 'home', '_format' => 'default'),
    'parameterPatterns' => array('_format' => 'html|json'), //allow html, json and/or other (like 'json|xml|atom') as alternative formats
    'method' => 'GET',
    'rights' => false
);