<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_widgets['test.exampleWidget'] = array(
    'controller' => 'Test_Default',
    'action' => 'widget',
    'parameter' => array(),
    'rights' => 0 //$rights->getRoleRights($rights->getRoleByKeyword('user'))
);