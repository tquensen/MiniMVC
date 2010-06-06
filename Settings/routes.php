<?php
$MiniMVC_routes['home'] = array(
    'route' => '',
    'controller' => 'My_Default',
    'action' => 'index',
    'parameter' => array('foo' => 'bar')
);
$MiniMVC_routes['baum'] = array(
    'route' => 'baum/:foo:/',
    'controller' => 'My_Default',
    'action' => 'index',
    'parameter' => array('foo' => 'bar')
);