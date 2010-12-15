<?php
$MiniMVC_routes['example.defaultIndex'] = array(
    'route' => 'page/index', // .format is optional (in brackets)
    'controller' => 'Example_Default',
    'action' => 'index',
    //'ajaxLayout' => array('html' => false), //disable the layout when requesting the html view via XMLHttpRequest
    'method' => 'GET',
    'rights' => false
);

//route for static pages, see controller/Default.php -> staticAction
$MiniMVC_routes['example.defaultStatic'] = array(
    'route' => 'page/:page:', // .format is optional (in brackets)
    'controller' => 'Example_Default',
    'action' => 'static',
    'parameter' => array('page' => 'home'), //set html as default format
    //'ajaxLayout' => array('html' => false), //disable the layout when requesting the html view via XMLHttpRequest
    'method' => 'GET',
    'rights' => false
);