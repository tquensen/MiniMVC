<?php $MiniMVC_settings = array (
  'modules' => 
  array (
    0 => 'Core',
    1 => 'My',
    2 => 'Dev',
    3 => 'User',
    4 => 'Blubb',
  ),
  'autoload' => 
  array (
  ),
  'apps' => 
  array (
    'backend' => 
    array (
      'baseurl' => 'http://scarax.ath.cx/MiniMVC/dev.php/backend/',
      'baseurlI18n' => 'http://scarax.ath.cx/MiniMVC/dev.php/backend/:lang:/',
      'baseurlStatic' => 'http://scarax.ath.cx/MiniMVC/',
    ),
    'frontend' => 
    array (
      'baseurl' => 'http://localhost/MiniMVC/web/index_dev.php/',
      'baseurlI18n' => 'http://scarax.ath.cx/MiniMVC/web/index_dev.php/:lang:/',
      'baseurlStatic' => 'http://localhost/MiniMVC/web/',
    ),
  ),
  'config' => 
  array (
    'defaultApp' => 'frontend',
    'defaultRoute' => 'home',
    'error401Route' => 'core.error401',
    'error403Route' => 'core.error403',
    'error404Route' => 'core.error404',
    'error500Route' => 'core.error500.debug',
    'defaultLanguage' => 'de',
    'enabledLanguages' => 
    array (
      0 => 'de',
      1 => 'en',
    ),
    'defaultLayout' => '',
    'autoloadPaths' => 
    array (
      0 => 'F:\\xampp\\htdocs\\MiniMVC/lib/Doctrine',
      1 => 'F:\\xampp\\htdocs\\MiniMVC/lib/sfComponents',
      2 => 'F:\\xampp\\htdocs\\MiniMVC/lib/MiniMVC',
      3 => 'F:\\xampp\\htdocs\\MiniMVC/lib',
      4 => 'F:\\xampp\\htdocs\\MiniMVC/module/',
    ),
    'registryClasses' => 
    array (
      'dispatcher' => 'MiniMVC_Dispatcher',
      'template' => 'MiniMVC_Layout',
      'guard' => 'MiniMVC_Guard',
      'rights' => 'MiniMVC_Rights',
      'db' => 'MiniMVC_Pdo',
      'helper' => 'MiniMVC_Helpers',
      'task' => 'MiniMVC_Task',
      'events' => 'MiniMVC_Events',
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
    'blubb' => 
    array (
      'someVar' => 'default value',
    ),
  ),
  'db' => 
  array (
    'default' => 
    array (
      'driver' => 'mysql:host=localhost;dbname=minimvc_mysqli',
      'username' => 'root',
      'password' => 'peniskopf',
    ),
    'doctrine' => 
    array (
      'dbtype' => 'mysql',
      'host' => 'localhost',
      'username' => 'root',
      'password' => 'peniskopf',
      'database' => 'minimvc',
      'prefix' => 'minimvc_',
    ),
    'doctrine2' => 
    array (
      'cacheClass' => '\\Doctrine\\Common\\Cache\\ArrayCache',
      'AutoGenerateProxyClasses' => true,
      'connection' => 
      array (
        'dbname' => 'minimvc_doctrine2',
        'user' => 'root',
        'password' => 'peniskopf',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
      ),
    ),
  ),
  'events' => 
  array (
    'minimvc.dispatcher.filterRoute' => 
    array (
      'core.example' => 
      array (
        'class' => 'Core_Events',
        'method' => 'initEvent',
        'instance' => 'once',
      ),
    ),
  ),
  'rights' => 
  array (
    'guest' => 
    array (
      'title' => 'For guests only',
      'key' => 1,
    ),
    'user' => 
    array (
      'title' => 'Basic user rights',
      'key' => 2,
    ),
    'moderate' => 
    array (
      'title' => 'Moderation rights',
      'key' => 256,
    ),
    'administrate' => 
    array (
      'title' => 'Administration rights',
      'key' => 1024,
    ),
  ),
  'roles' => 
  array (
    'guest' => 
    array (
      'title' => 'Default role for guests',
      'rights' => 1,
      'keyword' => 'guest',
    ),
    'user' => 
    array (
      'title' => 'Default registered user role',
      'rights' => 2,
      'keyword' => 'user',
    ),
    'moderator' => 
    array (
      'title' => 'Moderator role',
      'rights' => 258,
    ),
    'administrator' => 
    array (
      'title' => 'Administrator role',
      'rights' => 1282,
      'keyword' => 'admin',
    ),
    'superadmin' => 
    array (
      'title' => 'Superadmin (All rights)',
      'rights' => 1282,
    ),
  ),
  'routes' => 
  array (
    'home' => 
    array (
      'route' => '',
      'controller' => 'My_Default',
      'action' => 'index',
    ),
    'core.error401' => 
    array (
      'route' => 'error401',
      'controller' => 'Core_Error',
      'action' => 'error401',
    ),
    'core.error403' => 
    array (
      'route' => 'error403',
      'controller' => 'Core_Error',
      'action' => 'error403',
    ),
    'core.error404' => 
    array (
      'route' => 'error404',
      'controller' => 'Core_Error',
      'action' => 'error404',
    ),
    'core.error500' => 
    array (
      'route' => 'error500',
      'controller' => 'Core_Error',
      'action' => 'error500',
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
    ),
    'test' => 
    array (
      'route' => 'test',
      'controller' => 'My_Default',
      'action' => 'index',
      'parameter' => 
      array (
        'foo' => 'bar',
      ),
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
    ),
    'a' => 
    array (
      'route' => 'a/:foo:/:action:(\\.:_format:)?',
      'controller' => 'My_Default',
      'action' => 'test',
      'parameter' => 
      array (
        'foo' => 'bar',
        '_format' => 'html',
      ),
      'parameterPatterns' => 
      array (
        '_format' => '(html|json)',
      ),
    ),
    'dev.generateModule' => 
    array (
      'route' => 'dev/generate/module/:module:',
      'controller' => 'Dev_Generate',
      'action' => 'module',
    ),
    'dev.generateModels' => 
    array (
      'route' => 'dev/generate/model/:module:',
      'controller' => 'Dev_Generate',
      'action' => 'model',
    ),
    'dev.generateMigration' => 
    array (
      'route' => 'dev/generate/migration/:module:/:mode:',
      'controller' => 'Dev_Generate',
      'action' => 'migration',
    ),
    'dev.install' => 
    array (
      'route' => 'dev/install',
      'controller' => 'Dev_Install',
      'action' => 'module',
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
    'blubb.defaultIndex' => 
    array (
      'route' => 'blubb/index(\\.:_format:)?',
      'controller' => 'Blubb_Default',
      'action' => 'index',
      'parameter' => 
      array (
      ),
      'parameterPatterns' => 
      array (
        '_format' => 'json',
      ),
      'rights' => 0,
    ),
    'blubb.defaultIndex.json' => 
    array (
      'route' => 'blubb/index',
      'controller' => 'Blubb_Default',
      'action' => 'index',
      'format' => 'json',
      'parameter' => 
      array (
      ),
      'rights' => 0,
    ),
  ),
  'slots' => 
  array (
    'default' => 
    array (
      'sidebar' => 
      array (
      ),
    ),
  ),
  'tasks' => 
  array (
    'cc' => 
    array (
      'controller' => 'Core_Task',
      'action' => 'clearCache',
    ),
    'createLinks' => 
    array (
      'controller' => 'Core_Task',
      'action' => 'createLinks',
    ),
    'dev.generate.module' => 
    array (
      'controller' => 'Dev_Generate',
      'action' => 'module',
      'parameter' => 
      array (
        'module' => false,
      ),
    ),
    'dev.install.module' => 
    array (
      'controller' => 'Dev_Install',
      'action' => 'module',
      'parameter' => 
      array (
        'module' => false,
        'fromVersion' => 0,
        'type' => 'install',
      ),
    ),
    'dev.uninstall.module' => 
    array (
      'controller' => 'Dev_Install',
      'action' => 'module',
      'parameter' => 
      array (
        'module' => false,
        'fromVersion' => 'max',
        'type' => 'uninstall',
      ),
    ),
    'dev.install.model' => 
    array (
      'controller' => 'Dev_Install',
      'action' => 'model',
      'parameter' => 
      array (
        'model' => false,
        'fromVersion' => 0,
        'type' => 'install',
      ),
    ),
    'dev.uninstall.model' => 
    array (
      'controller' => 'Dev_Install',
      'action' => 'model',
      'parameter' => 
      array (
        'model' => false,
        'fromVersion' => 'max',
        'type' => 'uninstall',
      ),
    ),
    'dev.generate.model' => 
    array (
      'controller' => 'Dev_Generate',
      'action' => 'model',
      'parameter' => 
      array (
        'module' => false,
        'model' => false,
      ),
    ),
    'doctrine' => 
    array (
      'controller' => 'Dev_Doctrine',
      'action' => 'run',
    ),
  ),
  'widgets' => 
  array (
    'my.example' => 
    array (
      'controller' => 'My_Example',
      'action' => 'widget',
      'rights' => 0,
      'parameter' => 
      array (
      ),
    ),
    'user.login' => 
    array (
      'controller' => 'User_Login',
      'action' => 'login',
      'rights' => 1,
      'parameter' => 
      array (
        'widget' => true,
      ),
    ),
    'user.logout' => 
    array (
      'controller' => 'User_Login',
      'action' => 'logout',
      'rights' => 2,
      'parameter' => 
      array (
        'widget' => true,
      ),
    ),
    'blubb.exampleWidget' => 
    array (
      'controller' => 'Blubb_Default',
      'action' => 'widget',
      'parameter' => 
      array (
      ),
      'rights' => 0,
    ),
  ),
  'view' => 
  array (
    'css' => 
    array (
    ),
    'js' => 
    array (
    ),
  ),
);