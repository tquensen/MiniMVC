<?php
//define layouts for existing routes
//$MiniMVC_routes['home']['layout'] = 'singleColumn'; //for all formats (including html, json, xml, ...) - but in most cases you don't want that (see below)
//$MiniMVC_routes['home']['layout'] = array('html' => 'singleColumn'); // or only for specific formats (this will only change the html layout file, json/xml/... will use the default format)
//$MiniMVC_routes['home']['ajaxLayout'] = array('html' => 'plain'); //disable the layout when requesting the html view via XMLHttpRequest

//activate routes for active modules which are needed and are inactive by default
//$MiniMVC_routes['user.register']['active'] = true;

//or unset specific routes for active modules which are not needed and are active by default
//unset($MiniMVC_routes['user.register']);

//change access rights
/*
$MiniMVC_routes['something.secure']['rights'] = 'moderate'
$MiniMVC_routes['something.more.secure']['rights'] = array(array('administrate', array('moderate', 'publish')))
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