<?php
$MiniMVC_tasks['mongo.generate'] = array(
    'controller' => 'Mongo_Generate',
    'action' => 'model',
    'parameter' => array('module' => false, 'model' => false),
    'assign' => array('module', 'model')
);

$MiniMVC_tasks['mongo.generateEmbedded'] = array(
    'controller' => 'Mongo_Generate',
    'action' => 'embedded',
    'parameter' => array('module' => false, 'model' => false),
    'assign' => array('module', 'model')
);

$MiniMVC_tasks['mongo.install'] = array(
    'controller' => 'Mongo_Install',
    'action' => 'model',
    'parameter' => array('model' => false, 'fromVersion' => 0, 'toVersion' => 0, 'type' => 'install'),
    'assign' => array('model', 'fromVersion', 'toVersion')
);

$MiniMVC_tasks['mongo.uninstall'] = array(
    'controller' => 'Mongo_Install',
    'action' => 'model',
    'parameter' => array('model' => false, 'fromVersion' => 0, 'toVersion' => 0, 'type' => 'uninstall'),
    'assign' => array('model', 'fromVersion', 'toVersion')
);