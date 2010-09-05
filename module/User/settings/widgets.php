<?php
$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_widgets['user.login'] = array(
    'controller' => 'User_Login',
    'action' => 'login',
    'rights' => $rights->getRoleRights($rights->getRoleByKeyword('guest')),
    'parameter' => array('widget' => true),
    'hide' => 'user.login'
);
$MiniMVC_widgets['user.logout'] = array(
    'controller' => 'User_Login',
    'action' => 'logout',
    'rights' => $rights->getRoleRights($rights->getRoleByKeyword('user')),
    'parameter' => array('widget' => true),
    'hide' => 'user.logout'
);