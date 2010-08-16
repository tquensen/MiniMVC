<?php
//define layouts for existing routes
//$MiniMVC_routes['home']['layout'] = 'singleColumn';

//unset specific routes for active modules
//unset($MiniMVC_routes['user.register']);

//change access rights
/*
$rights = MiniMVC_Registry::getInstance()->rights;
$MiniMVC_routes['something.secure']['rights'] = $rights->getRoleRights($rights->getRoleByKeyword('admin'));
$MiniMVC_routes['something.more.secure']['rights'] = $rights->getRoleRights('SomeRoleName');
*/