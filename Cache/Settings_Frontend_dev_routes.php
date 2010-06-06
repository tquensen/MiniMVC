<?php $MiniMVC_routes = array (
  'home' => 
  array (
    'route' => '',
    'controller' => 'My_Default',
    'action' => 'index',
    'parameter' => 
    array (
      'foo' => 'bar',
    ),
    'routePattern' => '#^$#',
  ),
  'baum' => 
  array (
    'route' => 'baum/:foo:/',
    'controller' => 'My_Default',
    'action' => 'index',
    'parameter' => 
    array (
      'foo' => 'bar',
    ),
    'routePattern' => '#^baum/(?P<foo>[^/]+)/$#',
  ),
  'core.error401' => 
  array (
    'route' => 'error401',
    'controller' => 'Core_Error',
    'action' => 'error401',
    'routePattern' => '#^error401$#',
  ),
  'core.error403' => 
  array (
    'route' => 'error403',
    'controller' => 'Core_Error',
    'action' => 'error403',
    'routePattern' => '#^error403$#',
  ),
  'core.error404' => 
  array (
    'route' => 'error404',
    'controller' => 'Core_Error',
    'action' => 'error404',
    'routePattern' => '#^error404$#',
  ),
  'core.error500' => 
  array (
    'route' => 'error500',
    'controller' => 'Core_Error',
    'action' => 'error500',
    'routePattern' => '#^error500$#',
  ),
  'core.error500.debug' => 
  array (
    'route' => 'error500',
    'controller' => 'Core_Error',
    'action' => 'error500',
    'parameter' => 
    array (
      'debug' => true,
    ),
    'routePattern' => '#^error500$#',
  ),
  'dev.generateModule' => 
  array (
    'route' => 'dev/generate/module/:module:',
    'controller' => 'Dev_Generate',
    'action' => 'module',
    'routePattern' => '#^dev/generate/module/(?P<module>[^/]+)$#',
  ),
  'dev.generateModels' => 
  array (
    'route' => 'dev/generate/model/:module:',
    'controller' => 'Dev_Generate',
    'action' => 'model',
    'routePattern' => '#^dev/generate/model/(?P<module>[^/]+)$#',
  ),
  'dev.install' => 
  array (
    'route' => 'dev/install',
    'controller' => 'Dev_Install',
    'action' => 'module',
    'routePattern' => '#^dev/install$#',
  ),
  'user.register' => 
  array (
    'route' => 'user/register',
    'controller' => 'User_Register',
    'action' => 'register',
    'parameter' => 
    array (
    ),
    'rights' => 1,
    'layout' => '1column',
    'routePattern' => '#^user/register$#',
  ),
  'user.login' => 
  array (
    'route' => 'user/login',
    'controller' => 'User_Login',
    'action' => 'login',
    'parameter' => 
    array (
    ),
    'rights' => 1,
    'routePattern' => '#^user/login$#',
  ),
  'user.logout' => 
  array (
    'route' => 'user/logout',
    'controller' => 'User_Login',
    'action' => 'logout',
    'parameter' => 
    array (
    ),
    'rights' => 2,
    'routePattern' => '#^user/logout$#',
  ),
  'user.edit' => 
  array (
    'route' => 'user/edit',
    'controller' => 'User_Edit',
    'action' => 'edit',
    'parameter' => 
    array (
    ),
    'rights' => 2,
  ),
  'user.editPassword' => 
  array (
    'route' => 'user/editPassword',
    'controller' => 'User_Edit',
    'action' => 'editPassword',
    'parameter' => 
    array (
    ),
    'rights' => 2,
  ),
);