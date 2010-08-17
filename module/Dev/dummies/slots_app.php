<?php
//define slots and add module widgets or routes)
$MiniMVC_slots['sidebar'][] = array('name' => 'user.login', 'hide' => 'user.login');
$MiniMVC_slots['sidebar'][] = array('name' => 'user.logout', 'hide' => 'user.logout');

//some advanced examples
/*
$MiniMVC_slots['sidebar'][] = array(
    'type' => 'widget', //defaults to widget (possible values: widget or route)
    'name' => 'user.login',
    'layout' => 'all', //default to "all"
    'format' => 'all', //defaults to "html"
    'show' => 'home', //only show on these routes (string or array)
    'hide' => 'user.login', //hide widget on these routes (string or array)
    'parameter' => array() //set parameter values
);
$MiniMVC_slots['sidebar'][] = array(
    'type' => 'widget', //defaults to widget (possible values: widget or route)
    'name' => 'user.logout',
    'layout' => array('default', 'singleColumn'), //default to "all"
    'format' => array('html', 'mobile'), //defaults to "html"
    'show' => array('home', 'user.login', 'user.register'), //only show on these routes (string or array)
    'hide' => 'user.logout', //hide widget on these routes (string or array)
    'parameter' => array()
);
 */


