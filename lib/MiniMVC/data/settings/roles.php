<?php
$rights = MiniMVC_Registry::getInstance()->rights;

$MiniMVC_roles['guest'] = array(
    'title' => 'Default role for guests',
    'rights' => 'guest',
    'keyword' => 'guest'
);
$MiniMVC_roles['user'] = array(
    'title' => 'Default registered user role',
    'rights' => 'user',
    'keyword' => 'user'
);
$MiniMVC_roles['moderator'] = array(
    'title' => 'Moderator role',
    'rights' => array('user', 'moderate')
);
$MiniMVC_roles['author'] = array(
    'title' => 'Author role',
    'rights' => array('user', 'publish')
);
$MiniMVC_roles['administrator'] = array(
    'title' => 'Administrator role',
    'rights' => array('user', 'moderate', 'publish', 'administrate'),
    'keyword' => 'admin'
);
$MiniMVC_roles['superadmin'] = array(
    'title' => 'Superadmin (All rights)',
    'rights' => array('user', 'moderate', 'publish', 'administrate')
);