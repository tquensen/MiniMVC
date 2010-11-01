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

        $cache = $registry->settings->get('autoload');
        if ($file = $registry->settings->get('autoload/'.$class))
        {
            include_once $file;
            return;
        }

        $ns = explode('\\', $class);
        
        $classPath = str_replace('_', '/', $class);

        $parts = explode('/', $classPath);

        foreach ($registry->settings->get('config/autoloadPaths', array()) as $path) {
            $path = rtrim($path, '/');

            if (file_exists($path . '/' . $class . '.php')) {
                include_once ($path . '/' . $class . '.php');
                $registry->settings->set('autoload/'.$class, $path . '/' . $class . '.php');
                return;
            }
            elseif (file_exists($path . '/' . $class . '.class.php')) {
                include_once ($path . '/' . $class . '.class.php');
                $registry->settings->set('autoload/'.$class, $path . '/' . $class . '.class.php');
                return;
            }
            elseif (file_exists($path . '/class.' . $class . '.php')) {
                include_once ($path . '/class.' . $class . '.php');
                $registry->settings->set('autoload/'.$class, $path . '/class.' . $class . '.php');
                return;
            }
            elseif (file_exists($path . '/' . $classPath . '.php')) {
                include_once ($path . '/' . $classPath . '.php');
                $registry->settings->set('autoload/'.$class, $path . '/' . $classPath . '.php');
                return;
            }
            elseif (file_exists($path . '/' . $classPath . '.class.php')) {
                include_once ($path . '/' . $classPath . '.class.php');
                $registry->settings->set('autoload/'.$class, $path . '/' . $classPath . '.class.php');
                return;
            }
        }

        //if (in_array($parts[0], $registry->settings->get('modules'))) {
        $app = $registry->settings->get('runtime/currentApp');
        if (isset($parts[2]) && $parts[2] == 'Controller') {
            if (file_exists(APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php')) {
                include_once (APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php');
                $registry->settings->set('autoload/'.$class, APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php');
                return;
            }
            foreach (array_reverse($registry->settings->get('modules', array())) as $module) {
                if (is_file(MODULEPATH . $module . '/controller/' . $class . '.php')) {
                    include_once(MODULEPATH . $module . '/controller/' . $class . '.php');
                    $registry->settings->set('autoload/'.$class, MODULEPATH . $module . '/controller/' . $class . '.php');
                    return;
                }
            }
            if (is_file(MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php')) {
                include_once(MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php');
                $registry->settings->set('autoload/'.$class, MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php');
                return;
            }
        } else {
            if (file_exists(APPPATH . $app . '/module/' . $classPath . '.php')) {
                include_once (APPPATH . $app . '/module/' . $classPath . '.php');
                $registry->settings->set('autoload/'.$class, APPPATH . $app . '/module/' . $classPath . '.php');
                return;
            }

            if (file_exists(APPPATH . $app . '/module/' . $class . '.php')) {
                include_once (APPPATH . $app . '/module/' . $class . '.php');
                $registry->settings->set('autoload/'.$class, APPPATH . $app . '/module/' . $class . '.php');
                return;
            }

            if (file_exists(APPPATH . $app . '/module/lib/' . $classPath . '.php')) {
                include_once (APPPATH . $app . '/module/lib' . $classPath . '.php');
                $registry->settings->set('autoload/'.$class, APPPATH . $app . '/module/lib' . $classPath . '.php');
                return;
            }

            if (file_exists(APPPATH . $app . '/module/lib/' . $class . '.php')) {
                include_once (APPPATH . $app . '/module/lib' . $class . '.php');
                $registry->settings->set('autoload/'.$class, APPPATH . $app . '/module/lib' . $class . '.php');
                return;
            }

            if (file_exists(APPPATH . $app . '/module/model' . $classPath . '.php')) {
                include_once (APPPATH . $app . '/module/model' . $classPath . '.php');
                $registry->settings->set('autoload/'.$class, APPPATH . $app . '/module/model' . $classPath . '.php');
                return;
            }

            if (file_exists(APPPATH . $app . '/module/model' . $class . '.php')) {
                include_once (APPPATH . $app . '/module/model' . $class . '.php');
                $registry->settings->set('autoload/'.$class, APPPATH . $app . '/module/model' . $class . '.php');
                return;
            }

            if (file_exists(MODULEPATH . $classPath . '.php')) {
                include_once (MODULEPATH . $classPath . '.php');
                $registry->settings->set('autoload/'.$class, MODULEPATH . $classPath . '.php');
                return;
            }

            if (file_exists(MODULEPATH . $class . '.php')) {
                include_once (MODULEPATH . $class . '.php');
                $registry->settings->set('autoload/'.$class, MODULEPATH . $class . '.php');
                return;
            }

            if (file_exists(MODULEPATH . 'lib/' . $classPath . '.php')) {
                include_once (MODULEPATH . 'lib/' . $classPath . '.php');
                $registry->settings->set('autoload/'.$class, MODULEPATH . 'lib/' . $classPath . '.php');
                return;
            }

            if (file_exists(MODULEPATH . 'lib/' . $class . '.php')) {
                include_once (MODULEPATH . 'lib/' . $class . '.php');
                $registry->settings->set('autoload/'.$class, MODULEPATH . 'lib/' . $class . '.php');
                return;
            }

            if (file_exists(MODULEPATH . 'model/' . $classPath . '.php')) {
                include_once (MODULEPATH . 'model/' . $classPath . '.php');
                $registry->settings->set('autoload/'.$class, MODULEPATH . 'model/' . $classPath . '.php');
                return;
            }

            if (file_exists(MODULEPATH . 'model/' . $class . '.php')) {
                include_once (MODULEPATH . 'model/' . $class . '.php');
                $registry->settings->set('autoload/'.$class, MODULEPATH . 'model/' . $class . '.php');
                return;
            }
        }
        //}

        if (file_exists(BASEPATH . $classPath . '.php')) {
            include_once (BASEPATH . $classPath . '.php');
            $registry->settings->set('autoload/'.$class, BASEPATH . $classPath . '.php');
            return;
        }
    }
}

