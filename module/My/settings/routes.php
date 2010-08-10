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

$MiniMVC_routes['a'] = array(
    'route' => 'a/:foo:/:action:(\.:_format:)?',
    'controller' => 'My_Default',
    'action' => 'test',
    'parameter' => array('foo' => 'bar', '_format' => 'html'),
    'parameterPatterns' => array('_format' => '(html|json)')
);

