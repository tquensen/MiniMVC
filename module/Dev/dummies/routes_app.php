<?php
//define layouts for existing routes
//$MiniMVC_routes['home']['layout'] = 'singleColumn'; //for all formats (including html, json, xml, ...) - but in most cases you don't want that (see below)
//$MiniMVC_routes['home']['layout'] = array('html' => 'singleColumn'); // or only for specific formats (this will only change the html layout file, json/xml/... will use the default format)
//$MiniMVC_routes['home']['ajaxLayout'] = array('html' => false); //disable the layout when requesting the html view via XMLHttpRequest

//activate routes for active modules which are needed and are inactive by default
//$MiniMVC_routes['user.register']['active'] = true;

//or unset specific routes for active modules which are not needed and are active by default
//unset($MiniMVC_routes['user.register']);

//change access rights
/*
$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['something.secure']['rights'] = //$rights->getRights('administrate')
$MiniMVC_routes['something.more.secure']['rights'] = //$rights->getRights('administrate')
*/