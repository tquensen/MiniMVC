<?php
//define layouts for existing routes
//$MiniMVC_routes['home']['layout'] = 'singleColumn';

//activate routes for active modules which are needed and are inactive by default
//$MiniMVC_routes['user.register']['active'] = true;

//or unset specific routes for active modules which are not needed and are active by default
//unset($MiniMVC_routes['user.register']);

//change access rights
/*
$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['something.secure']['rights'] = $rights->getRoleRights($rights->getRoleByKeyword('admin'));
$MiniMVC_routes['something.more.secure']['rights'] = $rights->getRoleRights('SomeRoleName');
*/