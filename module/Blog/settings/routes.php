<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['blog.index'] = array(
    'route' => 'blog',
    'controller' => 'Blog_Post',
    'action' => 'index',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);
$MiniMVC_routes['blog.show'] = array(
    'route' => 'blog/:slug:',
    'controller' => 'Blog_Post',
    'action' => 'show',
    //'model' => array('BlogPost', 'slug'),
    'parameter' => array('slug' => false),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);
$MiniMVC_routes['blog.create'] = array(
    'route' => 'blog/create',
    'controller' => 'Blog_Post',
    'action' => 'create',
    'parameter' => array(),
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('admin'))
);
$MiniMVC_routes['blog.edit'] = array(
    'route' => 'blog/:slug:/edit',
    'controller' => 'Blog_Post',
    'action' => 'edit',
    'model' => array('BlogPost', 'slug'),
    'parameter' => array('slug' => false),
    'active' => false, //this route must be activated for each app to work
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('admin'))
);