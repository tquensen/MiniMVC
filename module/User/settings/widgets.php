<?php
$MiniMVC_widgets['user.login'] = array(
    'controller' => 'User_Login',
    'action' => 'login',
    'rights' => 'guest',
    'parameter' => array('widget' => true),
    'hide' => 'user.login'
);
$MiniMVC_widgets['user.logout'] = array(
    'controller' => 'User_Login',
    'action' => 'logout',
    'rights' => 'user',
    'parameter' => array('widget' => true),
    'hide' => 'user.logout'
);