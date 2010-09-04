<?php
$MiniMVC_routes['dev.generateModule'] = array(
    'route' => 'dev/generate/module/:module:',
    'controller' => 'Dev_Generate',
    'action' => 'module',
);
$MiniMVC_routes['dev.generateModels'] = array(
    'route' => 'dev/generate/model/:module:',
    'controller' => 'Dev_Generate',
    'action' => 'model',
);
$MiniMVC_routes['dev.generateMigration'] = array(
    'route' => 'dev/generate/migration/:module:/:mode:',
    'controller' => 'Dev_Generate',
    'action' => 'migration',
);
$MiniMVC_routes['dev.install'] = array(
    'route' => 'dev/install',
    'controller' => 'Dev_Install',
    'action' => 'module',
);