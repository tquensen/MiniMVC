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

        if ($cache = $registry->cache->get('autoload/'.$class))
        {
            include_once $cache;
            return;
        }

        if (strpos($class, '\\') !== false) {
            $className = ltrim(str_replace('\\','_',$class), '\\');
            $classpath = ltrim(str_replace('\\','/',$class), '\\');
        } else {
            $className = $class;
            $classPath = str_replace('_', '/', $class);
            $parts = explode('/', $classPath);
        }
        $app = $registry->settings->get('currentApp');

        if ($app) {
                if (file_exists(APPPATH . $app . '/' . $classPath . '.php')) {
                    include_once (APPPATH . $app . '/' . $classPath . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/' . $classPath . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/' . $className . '.php')) {
                    include_once (APPPATH . $app . '/' . $className . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/' . $className . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/lib/' . $classPath . '.php')) {
                    include_once (APPPATH . $app . '/lib/' . $classPath . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/lib/' . $classPath . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/lib/' . $className . '.php')) {
                    include_once (APPPATH . $app . '/lib/' . $className . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/lib/' . $className . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/model/' . $classPath . '.php')) {
                    include_once (APPPATH . $app . '/model/' . $classPath . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/model/' . $classPath . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/model/' . $className . '.php')) {
                    include_once (APPPATH . $app . '/model/' . $className . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/model/' . $className . '.php');
                    return;
                }
        }

        if (isset($parts[2]) && $parts[2] == 'Controller') {
            if ($app) {
                if (file_exists(APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php')) {
                    include_once (APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/module/' . $parts[0] . '/controller/' . $parts[1] . '.php');
                    return;
                }
            }
            foreach (array_reverse($registry->settings->get('modules', array())) as $module) {
                if (is_file(MODULEPATH . $module . '/controller/' . $className . '.php')) {
                    include_once(MODULEPATH . $module . '/controller/' . $className . '.php');
                    $registry->cache->set('autoload/'.$class, MODULEPATH . $module . '/controller/' . $className . '.php');
                    return;
                }
            }
            if (is_file(MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php')) {
                include_once(MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php');
                $registry->cache->set('autoload/'.$class, MODULEPATH . $parts[0] . '/controller/' . $parts[1] . '.php');
                return;
            }
        }

        foreach (array_reverse($registry->settings->get('modules', array())) as $module) {
            if ($app) {
                if (file_exists(APPPATH . $app . '/module/' . $module . '/' . $classPath . '.php')) {
                    include_once (APPPATH . $app . '/module/' . $module . '/' . $classPath . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/module/' . $module . '/' . $classPath . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/module/' . $module . '/' . $className . '.php')) {
                    include_once (APPPATH . $app . '/module/' . $module . '/' . $className . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/module/' . $module . '/' . $className . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/module/' . $module . '/lib/' . $classPath . '.php')) {
                    include_once (APPPATH . $app . '/module/' . $module . '/lib/' . $classPath . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/module/' . $module . '/lib/' . $classPath . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/module/' . $module . '/lib/' . $className . '.php')) {
                    include_once (APPPATH . $app . '/module/' . $module . '/lib/' . $className . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/module/' . $module . '/lib/' . $className . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/module/' . $module . '/model/' . $classPath . '.php')) {
                    include_once (APPPATH . $app . '/module/' . $module . '/model/' . $classPath . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/module/' . $module . '/model/' . $classPath . '.php');
                    return;
                }

                if (file_exists(APPPATH . $app . '/module/' . $module . '/model/' . $className . '.php')) {
                    include_once (APPPATH . $app . '/module/' . $module . '/model/' . $className . '.php');
                    $registry->cache->set('autoload/'.$class, APPPATH . $app . '/module/' . $module . '/model/' . $className . '.php');
                    return;
                }
            }

            if (file_exists(MODULEPATH . $module . '/' . $classPath . '.php')) {
                include_once (MODULEPATH . $module . '/' . $classPath . '.php');
                $registry->cache->set('autoload/'.$class, MODULEPATH . $module . '/' . $classPath . '.php');
                return;
            }

            if (file_exists(MODULEPATH . $module . '/' . $className . '.php')) {
                include_once (MODULEPATH . $module . '/' . $className . '.php');
                $registry->cache->set('autoload/'.$class, MODULEPATH . $module . '/' . $className . '.php');
                return;
            }

            if (file_exists(MODULEPATH . $module . '/lib/' . $classPath . '.php')) {
                include_once (MODULEPATH . $module . '/lib/' . $classPath . '.php');
                $registry->cache->set('autoload/'.$class, MODULEPATH . $module . '/lib/' . $classPath . '.php');
                return;
            }

            if (file_exists(MODULEPATH . $module . '/lib/' . $className . '.php')) {
                include_once (MODULEPATH . $module . '/lib/' . $className . '.php');
                $registry->cache->set('autoload/'.$class, MODULEPATH . $module . '/lib/' . $className . '.php');
                return;
            }

            if (file_exists(MODULEPATH . $module . '/model/' . $classPath . '.php')) {
                include_once (MODULEPATH . $module . '/model/' . $classPath . '.php');
                $registry->cache->set('autoload/'.$class, MODULEPATH . $module . '/model/' . $classPath . '.php');
                return;
            }

            if (file_exists(MODULEPATH . $module . '/model/' . $className . '.php')) {
                include_once (MODULEPATH . $module . '/model/' . $className . '.php');
                $registry->cache->set('autoload/'.$class, MODULEPATH . $module . '/model/' . $className . '.php');
                return;
            }
        }
        
        foreach ($registry->settings->get('config/autoloadPaths', array()) as $path) {
            $path = rtrim($path, '/');

            if (file_exists($path . '/' . $className . '.php')) {
                include_once ($path . '/' . $className . '.php');
                $registry->cache->set('autoload/'.$class, $path . '/' . $className . '.php');
                return;
            }
            elseif (file_exists($path . '/' . $className . '.class.php')) {
                include_once ($path . '/' . $className . '.class.php');
                $registry->cache->set('autoload/'.$class, $path . '/' . $className . '.class.php');
                return;
            }
            elseif (file_exists($path . '/class.' . $className . '.php')) {
                include_once ($path . '/class.' . $className . '.php');
                $registry->cache->set('autoload/'.$class, $path . '/class.' . $className . '.php');
                return;
            }
            elseif (file_exists($path . '/' . $classPath . '.php')) {
                include_once ($path . '/' . $classPath . '.php');
                $registry->cache->set('autoload/'.$class, $path . '/' . $classPath . '.php');
                return;
            }
            elseif (file_exists($path . '/' . $classPath . '.class.php')) {
                include_once ($path . '/' . $classPath . '.class.php');
                $registry->cache->set('autoload/'.$class, $path . '/' . $classPath . '.class.php');
                return;
            }
        }

        if (file_exists(BASEPATH . $classPath . '.php')) {
            include_once (BASEPATH . $classPath . '.php');
            $registry->cache->set('autoload/'.$class, BASEPATH . $classPath . '.php');
            return;
        }

        if (file_exists(BASEPATH . $className . '.php')) {
            include_once (BASEPATH . $className . '.php');
            $registry->cache->set('autoload/'.$class, BASEPATH . $className . '.php');
            return;
        }
    }
}

