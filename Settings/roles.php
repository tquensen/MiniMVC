<?php
$rights = MiniMVC_Registry::getInstance()->rights;

$MiniMVC_roles['guest'] = array(
    'title' => 'Default role for guests',
    'rights' => $rights->getRights('guest'),
    'keyword' => 'guest'
);
$MiniMVC_roles['user'] = array(
    'title' => 'Default registered user role',
    'rights' => $rights->getRights('user'),
    'keyword' => 'user'
);
$MiniMVC_roles['moderator'] = array(
    'title' => 'Moderator role',
    'rights' => $rights->getRights('user') | $rights->getRights('moderate')
);
$MiniMVC_roles['administrator'] = array(
    'title' => 'Administrator role',
    'rights' => $rights->getRights('user') | $rights->getRights('moderate') | $rights->getRights('administrate'),
    'keyword' => 'admin'
);
$MiniMVC_roles['superadmin'] = array(
    'title' => 'Superadmin (All rights)',
    'rights' => $rights->getAllRights() ^ $rights->getRights('guest'),
);