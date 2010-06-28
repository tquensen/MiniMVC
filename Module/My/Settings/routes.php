<?php
$MiniMVC_routes['test'] = array(
    'route' => 'test',
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