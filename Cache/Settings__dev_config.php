<?php $MiniMVC_config = array (
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
);