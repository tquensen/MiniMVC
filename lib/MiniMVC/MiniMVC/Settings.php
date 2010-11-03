<?php

/**
 * MiniMVC_Settings is responsible for all individual settings configured in the setting files
 */
class MiniMVC_Settings
{
    protected $settings = array();
    protected $changed = array();
    protected $files = array('modules', 'autoload', 'apps', 'config', 'db', 'events', 'rights', 'roles', 'routes', 'slots', 'tasks', 'widgets', 'view');

    /**
     *
     * @param mixed $environment the name of the environment to use or null to not use an environment
     * @param boolean $useCache if a cache should be used (recommended for production environments)
     */
    public function __construct($app = '', $environment = '', $useCache = true)
    {
        $this->set('runtime/currentApp', $app);
        $this->set('runtime/currentEnvironment', $environment);
        $this->set('runtime/useCache', $useCache);
    }

    protected function scanConfigFiles($app, $environment)
    {
        $this->settings[$app . '_' . $environment] = array();
        foreach ($this->files as $file) {
            $varname = 'MiniMVC_' . $file;
            $$varname = array();

            if (is_file(MINIMVCPATH . 'data/settings/' . $file . '.php')) {
                include(MINIMVCPATH . 'data/settings/' . $file . '.php');
            }

            if (is_file(DATAPATH . 'settings/' . $file . '.php')) {
                include(DATAPATH . 'settings/' . $file . '.php');
            }

            if ($environment) {
                if (is_file(MINIMVCPATH . 'data/settings/' . $file . '_' . $environment . '.php')) {
                    include(MINIMVCPATH . 'data/settings/' . $file . '_' . $environment . '.php');
                }

                if (is_file(DATAPATH . 'settings/' . $file . '_' . $environment . '.php')) {
                    include(DATAPATH . 'settings/' . $file . '_' . $environment . '.php');
                }
            }

            if ($file != 'modules') {
                foreach ($this->get('modules') as $module) {
                    if (is_file(MODULEPATH . $module . '/settings/' . $file . '.php')) {
                        include(MODULEPATH . $module . '/settings/' . $file . '.php');
                    }
                }

                foreach ($this->get('modules') as $module) {
                    if (is_file(MODULEPATH . $module . '/settings/' . $file . '_' . $environment . '.php')) {
                        include(MODULEPATH . $module . '/settings/' . $file . '_' . $environment . '.php');
                    }
                }
            }

            if ($app) {
                if (is_file(APPPATH . $app . '/settings/' . $file . '.php')) {
                    include(APPPATH . $app . '/settings/' . $file . '.php');
                }
                if (is_file(APPPATH . $app . '/settings/' . $file . '_' . $environment . '.php')) {
                    include(APPPATH . $app . '/settings/' . $file . '_' . $environment . '.php');
                }
            }

            $this->settings[$app . '_' . $environment][$file] = $$varname;
        }

        $this->save($app, $environment);
    }

    /**
     *
     * @param string $key the key
     * @param mixed $default the default value
     * @param mixed $app the name of the app to use or null for the current app
     * @param mixed $environment the name of the environment to use or null to use the current environment
     * @return array returns the settings as array
     */
    public function get($key, $default = null, $app = null, $environment = null)
    {
        if (isset($this->settings['runtime'][$key])) {
            return $this->settings['runtime'][$key];
        }

        $parts = explode('/', $key);

        $app = ($app) ? $app : $this->get('runtime/currentApp');
        $environment = ($environment) ? $environment : $this->get('runtime/currentEnvironment');

        if (!isset($this->settings[$app . '_' . $environment])) {
            if ($this->get('runtime/useCache') && is_file(CACHEPATH . 'settings_' . $app . '_' . $environment . '.php')) {
                include(CACHEPATH . 'settings_' . $app . '_' . $environment . '.php');
                $this->settings[$app . '_' . $environment] = $MiniMVC_settings;
            } else {
                $this->scanConfigFiles($app, $environment);
            }
        }

        $return = $this->settings[$app . '_' . $environment];
        while (null !== ($index = array_shift($parts))) {
            if (isset($return[$index])) {
                $return = &$return[$index];
            } else {
                $return = $default;
                break;
            }
        }
        return $return;
    }

    /**
     *
     * @param string $key the array key. multiple levels are divided by slashes ('modules', 'config/defaultApp', 'routes/home/controller')
     * @param mixed $value the new value
     * @param string|null $app the app to save the setting for or the current app if null
     * @param string|null $environment the environment to save the setting for or the current environment if null
     * @return bool whether the save was successful or not
     */
    public function set($key, $value, $app = null, $environment = null)
    {
        $parts = explode('/', $key);
        if (!$app && !$environment && $parts[0] === 'runtime') {
            $this->settings['runtime'][$key] = $value;
            return true;
        }

        $app = ($app) ? $app : $this->get('runtime/currentApp');
        $environment = ($environment) ? $environment : $this->get('runtime/currentEnvironment');

        if (!isset($this->settings[$app . '_' . $environment])) {
            return false;
        }

        $pointer = &$this->settings[$app . '_' . $environment];
        while (null !== ($index = array_shift($parts))) {
            if (count($parts) === 0) {
                break;
            }
            if (!isset($pointer[$index])) {
                $pointer[$index] = array();
            }
            $pointer = &$pointer[$index];
        }
        if ($value === null) {
            if (isset($pointer[$index])) {
                unset($pointer[$index]);
            }
        } else {
            $pointer[$index] = $value;
        }
        
        $this->changed[$app . '_' . $environment][$key] = $key;

        return true;
    }

    /**
     *
     * @param mixed $app the name of the app to use or null for the current app
     * @param mixed $environment the name of the environment to use or null to use the current environment
     */
    public function save($app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->get('runtime/currentApp');
        $environment = ($environment) ? $environment : $this->get('runtime/currentEnvironment');

        if ($this->get('runtime/useCache')) {
            file_put_contents(CACHEPATH.'settings_' . $app . '_' . $environment . '.php', '<?php ' . "\n" . $this->varExport($this->settings[$app . '_' . $environment], '$MiniMVC_settings', 100), LOCK_EX);
        }
    }

    /**
     * save all changes in the cache files
     */
    public function __destruct()
    {
        if ($this->get('runtime/useCache') && count($this->changed)) {
            foreach ($this->changed as $key => $changed) {
                list($app, $env) = explode('_', $key, 2);
                if (!is_file(CACHEPATH.'settings_' . $key . '.php')) {
                    continue;
                }
                include(CACHEPATH . 'settings_' . $key . '.php');
                if (!isset($MiniMVC_settings)) {
                    continue;
                }
                foreach ($changed as $changedKey) {
                    $value = $this->get($changedKey, null, $app, $env);
                    $parts = explode('/', $changedKey);
                    $pointer = &$MiniMVC_settings;
                    while (null !== ($index = array_shift($parts))) {
                        if (count($parts) === 0) {
                            break;
                        }
                        if (!isset($pointer[$index])) {
                            $pointer[$index] = array();
                        }
                        $pointer = &$pointer[$index];
                    }
                    $pointer[$index] = $value;
                }
                $this->settings[$key] = $MiniMVC_settings;
                $this->save($app, $env);
            }
        }
    }

    public function varExport($data, $varname, $maxDepth = 2, $depth = 0) {
        if (is_array($data) && $depth < $maxDepth) {
            $output = '';
            if ($depth == 0) {
                $output .= $varname.' = array();'."\n";
                $output .= "\n";
            }
            if (!count($data)) {
                $output .= $varname.' = array();'."\n";
            }
            foreach ($data as $key => $value) {
                $output .= $this->varExport($value, $varname . '[' . var_export($key, true) . ']', $maxDepth, $depth + 1);
            }
            if ($depth == 1) {
                $output .= "\n";
            }
            return $output;
        } else {
            return $varname . ' = ' . var_export($data, true) . ";\n";
        }
    }

}

