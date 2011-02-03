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