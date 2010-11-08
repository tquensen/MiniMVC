<?php
$MiniMVC_tasks['cc'] = array(
    'controller' => 'Core_Task',
    'action' => 'clearCache',
    'parameter' => array('rebuild' => false),
    'assign' => array('rebuild')
);

$MiniMVC_tasks['createLinks'] = array(
    'controller' => 'Core_Task',
    'action' => 'createLinks'
);