<?php $MiniMVC_config = array (
  'defaultApp' => 'Frontend',
  'defaultRoute' => 'home',
  'error401Route' => 'core.error401',
  'error403Route' => 'core.error403',
  'error404Route' => 'core.error404',
  'error500Route' => 'core.error500.debug',
  'defaultTemplate' => '',
  'defaultLanguage' => 'de',
  'enabledLanguages' => 
  array (
    0 => 'de',
    1 => 'en',
  ),
  'autoloadPaths' => 
  array (
    0 => 'Lib/Doctrine',
    1 => 'Module/User/Model/Base',
    2 => 'Module/User/Model',
    3 => 'Module/Dev/Model/Base',
    4 => 'Module/Dev/Model',
    5 => 'Module/My/Model/Base',
    6 => 'Module/My/Model',
    7 => 'Module/Core/Model/Base',
    8 => 'Module/Core/Model',
  ),
  'registryClasses' => 
  array (
    'dispatcher' => 'MiniMVC_Dispatcher',
    'template' => 'MiniMVC_Layout',
    'guard' => 'MiniMVC_Guard',
    'rights' => 'MiniMVC_Rights',
    'db' => 'MiniMVC_Db',
    'helper' => 'MiniMVC_Helpers',
  ),
  'user' => 
  array (
    'loginRedirect' => 
    array (
      'route' => 'home',
      'parameter' => 
      array (
      ),
      'app' => NULL,
    ),
    'logoutRedirect' => 
    array (
      'route' => 'home',
      'parameter' => 
      array (
      ),
      'app' => NULL,
    ),
  ),
  'modelPathsLoaded' => true,
);