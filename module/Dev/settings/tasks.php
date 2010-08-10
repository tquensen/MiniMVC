<?php

$MiniMVC_tasks['dev.generate.module'] = array(
    'controller' => 'Dev_Generate',
    'action' => 'module',
    'parameter' => array('module' => false)
);

$MiniMVC_tasks['dev.install.module'] = array(
    'controller' => 'Dev_Install',
    'action' => 'module',
    'parameter' => array('module' => false, 'fromVersion' => 0, 'type' => 'install')
);

$MiniMVC_tasks['dev.uninstall.module'] = array(
    'controller' => 'Dev_Install',
    'action' => 'module',
    'parameter' => array('module' => false, 'fromVersion' => 'max', 'type' => 'uninstall')
);

$MiniMVC_tasks['dev.install.model'] = array(
    'controller' => 'Dev_Install',
    'action' => 'model',
    'parameter' => array('model' => false, 'fromVersion' => 0, 'type' => 'install')
);

$MiniMVC_tasks['dev.uninstall.model'] = array(
    'controller' => 'Dev_Install',
    'action' => 'model',
    'parameter' => array('model' => false, 'fromVersion' => 'max', 'type' => 'uninstall')
);

$MiniMVC_tasks['dev.generate.model'] = array(
    'controller' => 'Dev_Generate',
    'action' => 'model',
    'parameter' => array('module' => false, 'model' => false)
);

$MiniMVC_tasks['doctrine'] = array(
    'controller' => 'Dev_Doctrine',
    'action' => 'run'
);
