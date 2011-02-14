<?php

// routes for controller FormTest
// ===================================

$MiniMVC_routes['formtest.index'] = array(
    'route' => 'formtest', // .format is optional (in brackets)
    'controller' => 'FormTest_FormTest',
    'action' => 'index',
    'method' => array('GET', 'POST'),
);
