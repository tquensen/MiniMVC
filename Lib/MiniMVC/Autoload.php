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

        $cache = $registry->settings->autoload;
        if (isset($cache[$class]))
        {
            include_once ($cache[$class]);
            return;
        }

        $ns = explode('\\', $class);
        
        $classPath = str_replace('_', '/', $class);

        $parts = explode('/', $classPath);

        if (isset($registry->settings->config['autoloadPaths'])) {

            foreach ($registry->settings->config['autoloadPaths'] as $path) {
                if (file_exists(BASEPATH . $path . '/' . $class . '.php')) {
                    include_once (BASEPATH . $path . '/' . $class . '.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = BASEPATH . $path . '/' . $class . '.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }
                elseif (file_exists(BASEPATH . $path . '/' . $class . '.class.php')) {
                    include_once (BASEPATH . $path . '/' . $class . '.class.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = BASEPATH . $path . '/' . $class . '.class.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }
                elseif (file_exists(BASEPATH . $path . '/class.' . $class . '.php')) {
                    include_once (BASEPATH . $path . '/class.' . $class . '.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = BASEPATH . $path . '/class.' . $class . '.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }
                elseif (file_exists(BASEPATH . $path . '/' . $classPath . '.php')) {
                    include_once (BASEPATH . $path . '/' . $classPath . '.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = BASEPATH . $path . '/' . $classPath . '.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }
                elseif (file_exists(BASEPATH . $path . '/' . $classPath . '.class.php')) {
                    include_once (BASEPATH . $path . '/' . $classPath . '.class.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = BASEPATH . $path . '/' . $classPath . '.class.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }
            }
        }

        if (in_array($parts[0], $registry->settings->modules)) {
            
            if (isset($parts[2]) && $parts[2] == 'Controller') {
                if (file_exists(APPPATH . $registry->settings->currentApp . '/Module/' . $parts[0] . '/Controller/' . $parts[1] . '.php')) {
                    include_once (APPPATH . $registry->settings->currentApp . '/Module/' . $parts[0] . '/Controller/' . $parts[1] . '.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = APPPATH . $registry->settings->currentApp . '/Module/' . $parts[0] . '/Controller/' . $parts[1] . '.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }
                foreach (array_reverse($registry->settings->modules) as $module) {
                    if (is_file(MODULEPATH . $module . '/Controller/' . $class . '.php')) {
                        include_once(MODULEPATH . $module . '/Controller/' . $class . '.php');
                        $cache = $registry->settings->autoload;
                        $cache[$class] = MODULEPATH . $module . '/Controller/' . $class . '.php';
                        $registry->settings->saveToCache('autoload', $cache);
                        return;
                    }
                }
                if (is_file(MODULEPATH . $parts[0] . '/Controller/' . $parts[1] . '.php')) {
                    include_once(MODULEPATH . $parts[0] . '/Controller/' . $parts[1] . '.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = MODULEPATH . $parts[0] . '/Controller/' . $parts[1] . '.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }
            } else {
                if (file_exists(APPPATH . $registry->settings->currentApp . '/Module/' . $classPath . '.php')) {
                    include_once (APPPATH . $registry->settings->currentApp . '/Module/' . $classPath . '.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = APPPATH . $registry->settings->currentApp . '/Module/' . $classPath . '.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }

                if (file_exists(MODULEPATH . $classPath . '.php')) {
                    include_once (MODULEPATH . $classPath . '.php');
                    $cache = $registry->settings->autoload;
                    $cache[$class] = MODULEPATH . $classPath . '.php';
                    $registry->settings->saveToCache('autoload', $cache);
                    return;
                }
            }
        }

        if (file_exists(BASEPATH . $classPath . '.php')) {
            include_once (BASEPATH . $classPath . '.php');
            $cache = $registry->settings->autoload;
            $cache[$class] = BASEPATH . $classPath . '.php';
            $registry->settings->saveToCache('autoload', $cache);
            return;
        }
    }
}

