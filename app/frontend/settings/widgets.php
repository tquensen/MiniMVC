<?php
//assign widgets to slots, define where it should be shown...
//$MiniMVC_widgets['some.example.widget']['slot'] = 'sidebar'; // or array('sidebar', 'sidebar2')
//$MiniMVC_widgets['some.example.widget']['layout'] = 'all'; //defaults to "all" / string or array / only show the widget on these layouts
//$MiniMVC_widgets['some.example.widget']['format'] = 'html'; //defaults to "html" / string or array / only show the widget on these formats
//$MiniMVC_widgets['some.example.widget']['hide'] = array('some.route', 'some.other.route'); // hide the widget on these routes
//
//modify access rights for module widgets
/*
$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_widgets['some.example.widget']['rights'] = $rights->getRoleRights($rights->getRoleByKeyword('user'));
 */