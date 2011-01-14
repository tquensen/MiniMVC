<?php
$MiniMVC_routes['my.index'] = array(
    'route' => 'page/index', // .format is optional (in brackets)
    'controller' => 'My_Default',
    'action' => 'index',
    //'ajaxLayout' => array('html' => false), //disable the layout when requesting the html view via XMLHttpRequest
    'method' => 'GET',
    'rights' => false
);

//route for static pages, see controller/Default.php -> staticAction
$MiniMVC_routes['my.static'] = array(
    'route' => 'page/:page:(.:_format:)', // .format is optional (in brackets)
    'controller' => 'My_Default',
    'action' => 'static',
    'parameter' => array('page' => 'home', '_format' => 'html'), //set html as default format
    //'ajaxLayout' => array('html' => false), //disable the layout when requesting the html view via XMLHttpRequest
    'method' => 'GET',
    'rights' => false
);