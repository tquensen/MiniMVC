<?php

/**
 * MiniMVC_FileCache is used for caching
 */
class MiniMVC_FileCache extends MiniMVC_Cache
{
    protected $data = array();

    public function get($key, $default = null, $app = null, $environment = null)
    {
        if (!$this->registry->settings->get('useCache')) {
            return $default;
        }
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if (!isset($this->data[$app.'_'.$environment])) {
            $this->load($app, $environment);
        }
        
        return isset($this->data[$app.'_'.$environment][$key]) ? $this->data[$app.'_'.$environment][$key] : $default;
    }

    public function set($key, $value, $merge = false, $app = null, $environment = null)
    {
        if (!$this->registry->settings->get('useCache')) {
            return false;
        }
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        $data = array();
        if (file_exists($this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.php')) {
            include $this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.php';
        }
        if ($merge && isset($data[$key])) {
            $data[$key] = array_merge((array) $data[$key], (array) $value);
        } else {
            $data[$key] = $value;
        }
        $this->save($data, $app, $environment);
        return true;
    }

    public function exists($key, $app = null, $environment = null)
    {
        if (!$this->registry->settings->get('useCache')) {
            return false;
        }
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if (!isset($this->data[$app.'_'.$environment])) {
            $this->load($app, $environment);
        }

        return isset($this->data[$app.'_'.$environment][$key]);
    }

    protected function load($app, $environment)
    {
        if (!file_exists($this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.php')) {
            file_put_contents($this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.tmp.php', '<?php $data = array();');
            rename($this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.tmp.php', $this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.php');
            $this->data[$app.'_'.$environment] = array();
        } else {
            $data = array();
            include $this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.php';
            $this->data[$app.'_'.$environment] = $data;
        }
    }

    protected function save($data, $app, $environment)
    {
        file_put_contents($this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.tmp.php', '<?php ' . "\n" . $this->varExport($data, '$data', 2));
        rename($this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.tmp.php', $this->folder.$this->prefix.'_cache_'.$app.'_'.$environment.'.php');
        $this->data[$app.'_'.$environment] = $data;
    }

    public function varExport($data, $varname, $maxDepth = 2, $depth = 0)
    {
        if (is_array($data) && $depth < $maxDepth) {
            $output = '';
            if ($depth == 0) {
                $output .= $varname . ' = array();' . "\n";
                $output .= "\n";
            }
            if (!count($data)) {
                $output .= $varname . ' = array();' . "\n";
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

