<?php
//$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_widgets['blog.exampleWidget'] = array(
    'controller' => 'Blog_Default',
    'action' => 'widget',
    'parameter' => array(),
    'rights' => 0, //$rights->getRoleRights($rights->getRoleByKeyword('user'))
    'layout' => 'all', //default to "all" (this should only be set in the app config)
    'format' => 'html', //defaults to "html" (this should only be set in the app config)
    'show' => 'home', //only show on these routes (string or array)
    'hide' => 'user.login', //hide widget on these routes (string or array)
);