<?php
//assign widgets to slots, define where it should be shown...
//$MiniMVC_widgets['some.example.widget']['slot'] = 'sidebar'; // or array('sidebar', 'sidebar2')
//$MiniMVC_widgets['some.example.widget']['position'] = 5; // or array('sidebar' => 5, 'sidebar2' => 3) / defaults to 0, use an array to define different positions for different slots
//$MiniMVC_widgets['some.example.widget']['layout'] = 'all'; //defaults to "all" / string or array / only show the widget on these layouts
//$MiniMVC_widgets['some.example.widget']['format'] = 'html'; //defaults to "html" / string or array / only show the widget on these formats / use 'all' to show the widget on all formats
//$MiniMVC_widgets['some.example.widget']['show'] = array('some.route', 'some.other.route'); // string or array / show the widget only on these routes
//$MiniMVC_widgets['some.example.widget']['hide'] = array('some.route', 'some.other.route'); // string or array / hide the widget on these routes

//modify access rights for module widgets
/*
$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_widgets['some.example.widget']['rights'] = $rights->getRights('user');
 */