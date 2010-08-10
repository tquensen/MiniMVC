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
            if (is_file(BASEPATH . 'Settings/' . $file . '.php')) {
                include(BASEPATH . 'Settings/' . $file . '.php');
            }

            if ($environment) {
                if (is_file(BASEPATH . 'Settings/' . $file . '_' . $environment . '.php')) {
                    include(BASEPATH . 'Settings/' . $file . '_' . $environment . '.php');
                }
            }

            if ($file != 'modules') {
                foreach ($this->get('modules') as $module) {
                    if (is_file(MODULEPATH . $module . '/Settings/' . $file . '.php')) {
                        include(MODULEPATH . $module . '/Settings/' . $file . '.php');
                    }
                }

                foreach ($this->get('modules') as $module) {
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
            if ($this->get('runtime/useCache') && is_file(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '.php')) {
                include(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '.php');
                $this->settings[$app . '_' . $environment] = $MiniMVC_Settings;
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
        $pointer[$index] = $value;

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
            file_put_contents(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '.php', '<?php $MiniMVC_Settings = ' . var_export($this->settings[$app . '_' . $environment], true) . ';');
        }
    }

    public function __destruct()
    {
        if ($this->get('runtime/useCache') && count($this->changed)) {
            foreach ($this->changed as $key => $changed) {
                list($app, $env) = explode('_', $key, 2);
                if (!is_file(BASEPATH . 'Cache/Settings_' . $key . '.php')) {
                    continue;
                }
                include(BASEPATH . 'Cache/Settings_' . $key . '.php');
                if (!isset($MiniMVC_Settings)) {
                    continue;
                }
                foreach ($changed as $changedKey) {
                    $value = $this->get($changedKey, null, $app, $env);
                    $parts = explode('/', $changedKey);
                    $pointer = &$MiniMVC_Settings;
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
                $this->settings[$key] = $MiniMVC_Settings;
                $this->save($app, $env);
            }
        }
    }

}

