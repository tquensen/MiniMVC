<?php
$MiniMVC_config['defaultApp'] = 'frontend';
$MiniMVC_config['defaultRoute'] = 'home';
$MiniMVC_config['error401Route'] = 'core.error401';
$MiniMVC_config['error403Route'] = 'core.error403';
$MiniMVC_config['error404Route'] = 'core.error404';
$MiniMVC_config['error500Route'] = 'core.error500';
$MiniMVC_config['defaultLanguage'] = 'en';
$MiniMVC_config['enabledLanguages'] = array('en', 'de');
$MiniMVC_config['defaultLayout'] = 'default';
$MiniMVC_config['autoloadPaths'] = array(BASEPATH.'lib/sfComponents', BASEPATH.'lib/MiniMVC', BASEPATH.'lib', MODULEPATH);
$MiniMVC_config['registryClasses'] = array(
    'dispatcher' => 'MiniMVC_Dispatcher',
    'template' => 'MiniMVC_Layout',
    'guard' => 'MiniMVC_Guard',
    'rights' => 'MiniMVC_Rights',
    'db' => 'MiniMVC_Pdo',
    'helper' => 'MiniMVC_Helpers',
    'task' => 'MiniMVC_Task',
    'events' => 'MiniMVC_Events'
);
