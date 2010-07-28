<?php

$MiniMVC_tasks['dev.generate.module'] = array(
    'controller' => 'Dev_Generate',
    'action' => 'module',
    'parameter' => array('module' => false)
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
