<?php
//assign widgets to slots, define where it should be shown...
//$MiniMVC_widgets['some.example.widget']['slot'] = 'sidebar'; // or array('sidebar', 'sidebar2')
//$MiniMVC_widgets['some.example.widget']['position'] = 5; // or array('sidebar' => 5, 'sidebar2' => 3) / defaults to 0, use an array to define different positions for different slots
//$MiniMVC_widgets['some.example.widget']['layout'] = 'all'; //defaults to "all" / string or array / only show the widget on these layouts
//$MiniMVC_widgets['some.example.widget']['format'] = 'default'; //defaults to "default" / string or array / only show the widget on these formats / use 'all' to show the widget on all formats
//$MiniMVC_widgets['some.example.widget']['show'] = array('some.route', 'some.other.route'); // string or array / show the widget only on these routes
//$MiniMVC_widgets['some.example.widget']['hide'] = array('some.route', 'some.other.route'); // string or array / hide the widget on these routes

//modify access rights for module widgets
/*
$MiniMVC_widgets['some.example.widget']['rights'] = 'user' // or array('user', array('moderate', 'publish'));
    // use false for no restrictions,
    // a right as string (e.g. 'user') th require that right,
    // an array of rights to require ALL of them (AND)
    //   e.g. array('user', 'publish') = user AND publish
    // a dimensional array to require at least one right (OR)
    //   e.g. array(array('user', 'guest')) = user OR guest
    // a combination of both (each new level switches logic between AND and OR)
    //   e.g. array('user', array('publish', 'administrate')) = user AND (publish OR administrate) = (user AND publish) OR (user AND administrate)
    //        array(array('administrate', array('moderate', 'publish'))) = administrate OR (moderate AND publish)
 */