<?php
/**
 * MiniMVC_Autoload does all the autoloading stuff
 */
class MiniMVC_Autoload
{
    /**
     *
     * @param string $class the name of the class to load
     * @return null
     */
    public static function autoload($class)
    {
        $registry = MiniMVC_Registry::getInstance();

        $cache = $registry->cache->get('autoload');
        if (isset($cache[$class]))
        {
            include_once $cache[$class];
            return;
        }

        $ns = explode('\\', $class);
        
        $classPath = str_replace('_', '/', $class);

        $parts = explode('/', $classPath);

        foreach ($registry->settings->get('config/autoloadPaths', array()) as $path) {
            $path = rtrim($path, '/');

            if (file_exists($path . '/' . $class . '.php')) {
                include_once ($path . '/' . $class . '.php');
                $registry->cache->set('autoload', array($class => $path . '/' . $class . '.php'), true);
                return;
            }
            elseif (file_exists($path . '/' . $class . '.class.php')) {
                include_once ($path . '/' . $class . '.class.php');
                $registry->cache->set('autoload', array($class => $path . '/' . $class . '.class.php'), true);
                return;
            }
            elseif (file_exists($path . '/class.' . $class . '.php')) {
                include_once ($path . '/class.' . $class . '.php');
                $registry->cache->set('autoload', array($class => $path . '/class.' . $class . '.php'), true);
                return;
            }
            elseif (file_exists($path . '/' . $classPath . '.php')) {
                include_once ($path . '/' . $classPath . '.php');
                $registry->cache->set('autoload', array($class => $path . '/' . $classPath . '.php'), true);
                return;
            }
            elseif (file_exists($path . '/' . $classPath . '.class.php')) {
                include_once ($path . '/' . $classPath . '.class.php');
                $registry->cache->set('autoload', array($class => $path . '/' . $classPath . '.class.php'), true);
                return;
            }
        }

        //if (in_array($parts[0], $registry->settings->get('modules'))) {
        $app = $registry->settings->get('currentApp');
        if (isset($parts[2]) && $parts[2] == 'Controller') {
            if (file_exists(APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php')) {
                include_once (APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php');
                $registry->cache->set('autoload', array($class => APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php'), true);
                return;
            }
            foreach (array_reverse($registry->settings->get('modules', array())) as $module) {
                if (is_file(MODULEPATH . $module . '/controller/' . $class . '.php')) {
                    include_once(MODULEPATH . $module . '/controller/' . $class . '.php');
                    $registry->cache->set('autoload', array($class => MODULEPATH . $module . '/controller/' . $class . '.php'), true);
                    return;
                }
            }
            if (is_file(MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php')) {
                include_once(MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php');
                $registry->cache->set('autoload', array($class => MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php'), true);
                return;
            }
        }
        foreach (array_reverse($registry->settings->get('modules', array())) as $module) {
            if (file_exists(APPPATH . $app . '/module/' . $module . '/' . $classPath . '.php')) {
                include_once (APPPATH . $app . '/module/' . $module . '/' . $classPath . '.php');
                $registry->cache->set('autoload', array($class => APPPATH . $app . '/module/' . $module . '/' . $classPath . '.php'), true);
                return;
            }

            if (file_exists(APPPATH . $app . '/module/' . $module . '/' . $class . '.php')) {
                include_once (APPPATH . $app . '/module/' . $module . '/' . $class . '.php');
                $registry->cache->set('autoload', array($class => APPPATH . $app . '/module/' . $module . '/' . $class . '.php'), true);
                return;
            }

            if (file_exists(APPPATH . $app . '/module/' . $module . '/lib/' . $classPath . '.php')) {
                include_once (APPPATH . $app . '/module/' . $module . '/lib/' . $classPath . '.php');
                $registry->cache->set('autoload', array($class => APPPATH . $app . '/module/' . $module . '/lib/' . $classPath . '.php'), true);
                return;
            }

            if (file_exists(APPPATH . $app . '/module/' . $module . '/lib/' . $class . '.php')) {
                include_once (APPPATH . $app . '/module/' . $module . '/lib/' . $class . '.php');
                $registry->cache->set('autoload', array($class => APPPATH . $app . '/module/' . $module . '/lib/' . $class . '.php'), true);
                return;
            }

            if (file_exists(APPPATH . $app . '/module/' . $module . '/model/' . $classPath . '.php')) {
                include_once (APPPATH . $app . '/module/' . $module . '/model/' . $classPath . '.php');
                $registry->cache->set('autoload', array($class => APPPATH . $app . '/module/' . $module . '/model/' . $classPath . '.php'), true);
                return;
            }

            if (file_exists(APPPATH . $app . '/module/' . $module . '/model/' . $class . '.php')) {
                include_once (APPPATH . $app . '/module/' . $module . '/model/' . $class . '.php');
                $registry->cache->set('autoload', array($class => APPPATH . $app . '/module/' . $module . '/model/' . $class . '.php'), true);
                return;
            }

            if (file_exists(MODULEPATH . $classPath . '.php')) {
                include_once (MODULEPATH . $classPath . '.php');
                $registry->cache->set('autoload', array($class => MODULEPATH . $classPath . '.php'), true);
                return;
            }

            if (file_exists(MODULEPATH . $class . '.php')) {
                include_once (MODULEPATH . $class . '.php');
                $registry->cache->set('autoload', array($class => MODULEPATH . $class . '.php'), true);
                return;
            }

            if (file_exists(MODULEPATH . $module . '/lib/' . $classPath . '.php')) {
                include_once (MODULEPATH . $module . '/lib/' . $classPath . '.php');
                $registry->cache->set('autoload', array($class => MODULEPATH . $module . '/lib/' . $classPath . '.php'), true);
                return;
            }

            if (file_exists(MODULEPATH . $module . '/lib/' . $class . '.php')) {
                include_once (MODULEPATH . $module . '/lib/' . $class . '.php');
                $registry->cache->set('autoload', array($class => MODULEPATH . $module . '/lib/' . $class . '.php'), true);
                return;
            }

            if (file_exists(MODULEPATH . $module . '/model/' . $classPath . '.php')) {
                include_once (MODULEPATH . $module . '/model/' . $classPath . '.php');
                $registry->cache->set('autoload', array($class => MODULEPATH . $module . '/model/' . $classPath . '.php'), true);
                return;
            }

            if (file_exists(MODULEPATH . $module . '/model/' . $class . '.php')) {
                include_once (MODULEPATH . $module . '/model/' . $class . '.php');
                $registry->cache->set('autoload', array($class => MODULEPATH . $module . '/model/' . $class . '.php'), true);
                return;
            }
        }
        //}

        if (file_exists(BASEPATH . $classPath . '.php')) {
            include_once (BASEPATH . $classPath . '.php');
            $registry->cache->set('autoload', array($class => BASEPATH . $classPath . '.php'), true);
            return;
        }
    }
}

