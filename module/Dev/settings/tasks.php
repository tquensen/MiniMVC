<?php

$MiniMVC_tasks['generate.module'] = array(
    'controller' => 'Dev_Generate',
    'action' => 'module',
    'parameter' => array('module' => false),
    'assign' => 'module'
);

$MiniMVC_tasks['install.module'] = array(
    'controller' => 'Dev_Install',
    'action' => 'module',
    'parameter' => array('module' => false, 'fromVersion' => 0, 'toVersion' => 0, 'type' => 'install'),
    'assign' => array('module', 'fromVersion', 'toVersion')
);

$MiniMVC_tasks['uninstall.module'] = array(
    'controller' => 'Dev_Install',
    'action' => 'module',
    'parameter' => array('module' => false, 'fromVersion' => 0, 'toVersion' => 0, 'type' => 'uninstall'),
    'assign' => array('module', 'fromVersion', 'toVersion')
);

$MiniMVC_tasks['generate.model'] = array(
    'controller' => 'Dev_Generate',
    'action' => 'model',
    'parameter' => array('module' => false, 'model' => false),
    'assign' => array('module', 'model')
);

$MiniMVC_tasks['install.model'] = array(
    'controller' => 'Dev_Install',
    'action' => 'model',
    'parameter' => array('model' => false, 'fromVersion' => 0, 'type' => 'install'),
    'assign' => array('model', 'fromVersion')
);

$MiniMVC_tasks['uninstall.model'] = array(
    'controller' => 'Dev_Install',
    'action' => 'model',
    'parameter' => array('model' => false, 'fromVersion' => 'max', 'type' => 'uninstall'),
    'assign' => array('model', 'fromVersion')
);

$MiniMVC_tasks['generate.controller'] = array(
    'controller' => 'Dev_Generate',
    'action' => 'controller',
    'parameter' => array('module' => false, 'controller' => false),
    'assign' => array('module', 'controller')
);

$MiniMVC_tasks['generate.app'] = array(
    'controller' => 'Dev_Generate',
    'action' => 'app',
    'parameter' => array('app' => false, 'w' => true),
    'assign' => 'app'
);

$MiniMVC_tasks['doctrine'] = array(
    'controller' => 'Dev_Doctrine',
    'action' => 'run'
);
