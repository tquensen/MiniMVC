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
            if (file_exists(BASEPATH . $path . '/' . $class . '.php')) {
                include_once (BASEPATH . $path . '/' . $class . '.php');
                $registry->settings->set('autoload/'.$class, BASEPATH . $path . '/' . $class . '.php');
                return;
            }
            elseif (file_exists(BASEPATH . $path . '/' . $class . '.class.php')) {
                include_once (BASEPATH . $path . '/' . $class . '.class.php');
                $registry->settings->set('autoload/'.$class, BASEPATH . $path . '/' . $class . '.class.php');
                return;
            }
            elseif (file_exists(BASEPATH . $path . '/class.' . $class . '.php')) {
                include_once (BASEPATH . $path . '/class.' . $class . '.php');
                $registry->settings->set('autoload/'.$class, BASEPATH . $path . '/class.' . $class . '.php');
                return;
            }
            elseif (file_exists(BASEPATH . $path . '/' . $classPath . '.php')) {
                include_once (BASEPATH . $path . '/' . $classPath . '.php');
                $registry->settings->set('autoload/'.$class, BASEPATH . $path . '/' . $classPath . '.php');
                return;
            }
            elseif (file_exists(BASEPATH . $path . '/' . $classPath . '.class.php')) {
                include_once (BASEPATH . $path . '/' . $classPath . '.class.php');
                $registry->settings->set('autoload/'.$class, BASEPATH . $path . '/' . $classPath . '.class.php');
                return;
            }
        }

        if (in_array($parts[0], $registry->settings->get('modules'))) {
            $app = $registry->settings->get('runtime/currentApp');
            if (isset($parts[2]) && $parts[2] == 'Controller') {
                if (file_exists(APPPATH . $app . '/Module/' . $parts[0] . '/Controller/' . $parts[1] . '.php')) {
                    include_once (APPPATH . $app . '/Module/' . $parts[0] . '/Controller/' . $parts[1] . '.php');
                    $registry->settings->set('autoload/'.$class, APPPATH . $app . '/Module/' . $parts[0] . '/Controller/' . $parts[1] . '.php');
                    return;
                }
                foreach (array_reverse($registry->settings->get('modules', array())) as $module) {
                    if (is_file(MODULEPATH . $module . '/Controller/' . $class . '.php')) {
                        include_once(MODULEPATH . $module . '/Controller/' . $class . '.php');
                        $registry->settings->set('autoload/'.$class, MODULEPATH . $module . '/Controller/' . $class . '.php');
                        return;
                    }
                }
                if (is_file(MODULEPATH . $parts[0] . '/Controller/' . $parts[1] . '.php')) {
                    include_once(MODULEPATH . $parts[0] . '/Controller/' . $parts[1] . '.php');
                    $registry->settings->set('autoload/'.$class, MODULEPATH . $parts[0] . '/Controller/' . $parts[1] . '.php');
                    return;
                }
            } else {
                if (file_exists(APPPATH . $app . '/Module/' . $classPath . '.php')) {
                    include_once (APPPATH . $app . '/Module/' . $classPath . '.php');
                    $registry->settings->set('autoload/'.$class, APPPATH . $app . '/Module/' . $classPath . '.php');
                    return;
                }

                if (file_exists(MODULEPATH . $classPath . '.php')) {
                    include_once (MODULEPATH . $classPath . '.php');
                    $registry->settings->set('autoload/'.$class, MODULEPATH . $classPath . '.php');
                    return;
                }
            }
        }

        if (file_exists(BASEPATH . $classPath . '.php')) {
            include_once (BASEPATH . $classPath . '.php');
            $registry->settings->set('autoload/'.$class, BASEPATH . $classPath . '.php');
            return;
        }
    }
}

