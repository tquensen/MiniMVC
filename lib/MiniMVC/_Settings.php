<?php

/**
 * MiniMVC_Settings is responsible for all individual settings configured in the setting files
 *
 * @property array $config general settings
 * @property array $apps application settings (paths)
 * @property array $db database settings
 * @property array $modules active modules
 * @property array $rights user rights
 * @property array $roles user roles
 * @property array $slots layout slots for widgets or routes
 * @property array $tasks cli tasks
 * @property array $view view related settings
 * @property array $widgets widgets to display in slots
 * @property array $events attached events
 */
class MiniMVC_Settings
{
    protected $dummy = array();
    protected $settings = null;
    protected $files = array('modules', 'autoload', 'apps', 'config', 'db', 'events', 'rights', 'roles', 'routes', 'slots', 'tasks', 'widgets', 'view');

    /**
     *
     * @param mixed $environment the name of the environment to use or null to not use an environment
     * @param boolean $useCache if a cache should be used (recommended for production environments)
     */
    public function __construct($app = '', $environment = '', $useCache = true)
    {
        $this->currentApp = $app;
        $this->currentEnvironment = $environment;
        $this->useCache = $useCache;

        //$this->scanConfigFiles($this->currentApp, $this->currentEnvironment);
    }

    protected function scanConfigFiles($app, $environment)
    {
        foreach ($this->files as $file) {
            $varname = 'MiniMVC_' . $file;
            $$varname = array();
            if (is_file(BASEPATH . 'Settings/' . $file . '.php')) {
                include(BASEPATH . 'Settings/' . $file . '.php');
            }

            if ($environment) {
                if (is_file(BASEPATH . 'Settings/' . $file . '_' . $environment . '.php')) {
                    include(BASEPATH . 'Settings/' . $file . '_' . $environment . '.php');
                }
            }

            if ($file != 'modules') {
                foreach ($this->modules as $module) {
                    if (is_file(MODULEPATH . $module . '/Settings/' . $file . '.php')) {
                        include(MODULEPATH . $module . '/Settings/' . $file . '.php');
                    }
                }

                foreach ($this->modules as $module) {
                    if (is_file(MODULEPATH . $module . '/Settings/' . $file . '_' . $environment . '.php')) {
                        include(MODULEPATH . $module . '/Settings/' . $file . '_' . $environment . '.php');
                    }
                }
            }

            if ($app) {
                if (is_file(APPPATH . $app . '/Settings/' . $file . '.php')) {
                    include(APPPATH . $app . '/Settings/' . $file . '.php');
                }
                if (is_file(APPPATH . $app . '/Settings/' . $file . '_' . $environment . '.php')) {
                    include(APPPATH . $app . '/Settings/' . $file . '_' . $environment . '.php');
                }
            }

            $this->settings[$app . '_' . $environment][$file] = $$varname;
        }

        $this->saveToCache(null, null, $app, $environment);
    }

    /**
     *
     * @param string $key the key used to store the data
     * @param mixed $value the value to store
     */
    public function __set($key, $value)
    {
        $this->settings['runtime'][$key] = $value;
    }

    /**
     *
     * @param string $file the name of the settings file to use
     * @return array returns the settings as array
     */
    public function &__get($file)
    {
        return $this->get($file);
    }

    /**
     *
     * @param string $file the name of the settings file to use
     * @param mixed $app the name of the app to use or null for the current app
     * @param mixed $environment the name of the environment to use or null to use the current environment
     * @return array returns the settings as array
     */
    public function &get($file, $app = null, $environment = null)
    {
        
        if (isset($this->settings['runtime'][$file]) && !$app && !$environment) {
            return $this->settings['runtime'][$file];
        }

        $app = ($app) ? $app : $this->get('currentApp');
        $environment = ($environment) ? $environment : $this->get('currentEnvironment');

        if (isset($this->settings[$app . '_' . $environment])) {
            return $this->settings[$app . '_' . $environment][$file];
        }

        if ($this->useCache && is_file(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '.php')) {
            include(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '.php');
            $this->settings[$app . '_' . $environment] = $MiniMVC_Settings;
            return $this->settings[$app . '_' . $environment][$file];
        }

        $this->scanConfigFiles($app, $environment);

        return $this->settings[$app . '_' . $environment][$file];
    }

    /**
     *
     * @param string $file the name of the settings file
     * @param array $data the data to store
     * @param mixed $app the name of the app to use or null for the current app
     * @param mixed $environment the name of the environment to use or null to use the current environment
     */
    public function saveToCache($file, $data, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->get('currentApp');
        $environment = ($environment) ? $environment : $this->get('currentEnvironment');

        if ($file) {
            $this->settings[$app . '_' . $environment][$file] = $data;
        }
        if ($this->useCache) {
            file_put_contents(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '.php', '<?php $MiniMVC_Settings = ' . var_export($this->settings[$app . '_' . $environment], true) . ';');
        }
    }

}

