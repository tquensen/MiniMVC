<?php
$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['user.register'] = array(
    'route' => 'user/register',
    'controller' => 'User_Register',
    'action' => 'register',
    'parameter' => array(),
    'rights' => 'guest'
);
$MiniMVC_routes['user.login'] = array(
    'route' => 'user/login',
    'controller' => 'User_Login',
    'action' => 'login',
    'parameter' => array(),
    'rights' => 'guest'
);
$MiniMVC_routes['user.logout'] = array(
    'route' => 'user/logout',
    'controller' => 'User_Login',
    'action' => 'logout',
    'parameter' => array(),
    'rights' => 'user'
);
$MiniMVC_routes['user.edit'] = array(
    'route' => 'user/edit',
    'controller' => 'User_Edit',
    'action' => 'edit',
    'parameter' => array(),
    'rights' => 'user'
);
$MiniMVC_routes['user.editPassword'] = array(
    'route' => 'user/editPassword',
    'controller' => 'User_Edit',
    'action' => 'editPassword',
    'parameter' => array(),
    'rights' => 'user'
);