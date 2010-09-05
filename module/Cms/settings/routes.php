<?php
$rights = MiniMVC_Registry::getInstance()->rights;

$MiniMVC_routes['cms.create'] = array(
    'route' => 'cms/create',
    'controller' => 'Cms_Default',
    'action' => 'create',
    'parameter' => array(),
    //'active' => false, //this route must be activated for each app to work
    'rights' => $rights->getRoleRights($this->get('config/cms/authorRights')) //$rights->getRoleRights($rights->getRoleByKeyword('author'))
);

$MiniMVC_routes['cms.edit'] = array(
    'route' => 'cms/:slug:/edit',
    'controller' => 'Cms_Default',
    'action' => 'edit',
    'parameter' => array(),
    'rights' => $rights->getRoleRights($this->get('config/cms/authorRights'))
);

$MiniMVC_routes['cms.show'] = array(
    'route' => 'cms/:slug:',
    'controller' => 'Cms_Default',
    'action' => 'show',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);

