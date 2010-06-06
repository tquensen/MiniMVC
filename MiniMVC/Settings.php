<?php

class MiniMVC_Settings
{
    protected $settings = null;

    public function __construct($environment = null, $useCache = true)
    {
        $this->currentApp = '';
        $this->currentEnvironment = $environment;
        $this->useCache = $useCache;
    }

    public function __set($key, $value)
    {
        $this->settings['runtime'][$key] = $value;
    }

    public function __get($file)
    {
        return $this->get($file);
    }

    public function get($file, $app = null, $environment = null)
    {
        if (isset($this->settings['runtime'][$file]) && ! $app && ! $environment) {
            return $this->settings['runtime'][$file];
        }

        $app = ($app) ? $app : $this->currentApp;
        $environment = ($environment) ? $environment : $this->currentEnvironment;

        if (isset($this->settings[$file . '_' . $app . '_' . $environment])) {
            return $this->settings[$file . '_' . $app . '_' . $environment];
        }

        $varname = 'MiniMVC_' . $file;
        $$varname = array();

        if ($this->useCache && is_file(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '_' . $file . '.php')) {
            include(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '_' . $file . '.php');
            $this->settings[$file . '_' . $app . '_' . $environment] = $$varname;
            return $this->settings[$file . '_' . $app . '_' . $environment];
        }

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
                if (is_file(BASEPATH . 'Module/' . $module . '/Settings/' . $file . '.php')) {
                    include(BASEPATH . 'Module/' . $module . '/Settings/' . $file . '.php');
                }
            }

            foreach ($this->modules as $module) {
                if (is_file(BASEPATH . 'Module/' . $module . '/Settings/' . $file . '_' . $environment . '.php')) {
                    include(BASEPATH . 'Module/' . $module . '/Settings/' . $file . '_' . $environment . '.php');
                }
            }
        }

        if ($app) {
            if (is_file(BASEPATH . 'App/' . $app . '/Settings/' . $file . '.php')) {
                include(BASEPATH . 'App/' . $app . '/Settings/' . $file . '.php');
            }
            if (is_file(BASEPATH . 'App/' . $app . '/Settings/' . $file . '_' . $environment . '.php')) {
                include(BASEPATH . 'App/' . $app . '/Settings/' . $file . '_' . $environment . '.php');
            }
        }

        $this->saveToCache($file, $$varname, $app, $environment);

        return $$varname;
    }

    public function saveToCache($file, $data, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->currentApp;
        $environment = ($environment) ? $environment : $this->currentEnvironment;

        $this->settings[$file . '_' . $app . '_' . $environment] = $data;
        if ($this->useCache) {
            file_put_contents(BASEPATH . 'Cache/Settings_' . $app . '_' . $environment . '_' . $file . '.php', '<?php $MiniMVC_' . $file . ' = ' . var_export($data, true) . ';');
        }
    }

}

