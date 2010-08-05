<?php
$MiniMVC_config['defaultRoute'] = 'home';
$MiniMVC_config['error401Route'] = 'core.error401';
$MiniMVC_config['error403Route'] = 'core.error403';
$MiniMVC_config['error404Route'] = 'core.error404';
$MiniMVC_config['error500Route'] = 'core.error500';
$MiniMVC_config['defaultLanguage'] = 'de';
$MiniMVC_config['enabledLanguages'] = array('de', 'en');
$MiniMVC_config['defaultLayout'] = '';
$MiniMVC_config['autoloadPaths'] = array('Lib/Doctrine', 'Lib/sfComponents', 'Lib', 'Module');
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
