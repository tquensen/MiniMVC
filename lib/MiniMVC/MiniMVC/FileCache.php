<?php

/**
 * MiniMVC_FileCache is used for caching
 */
class MiniMVC_FileCache extends MiniMVC_Cache
{
    protected $data = array();
    protected $toSave = array();
    
    public function get($key, $default = null)
    {
        $parts = explode('/', $key);
        $file = array_shift($parts);
        if (!$file) {
            return $default;
        }

        $app = $this->registry->settings->get('currentApp');
        $environment = $this->registry->settings->get('currentEnvironment');

        if (!isset($this->data[$file])) {
            $this->load($file, $app, $environment);
        }

        $return = $this->data[$file];
        if (count($parts) === 0 && !count($return)) {
            return $default;
        } elseif (count($parts) === 1) {
            return isset($return[$parts[0]]) ? $return[$parts[0]] : $default;
        } elseif (count($parts) === 2) {
            return isset($return[$parts[0]][$parts[1]]) ? $return[$parts[0]][$parts[1]] : $default;
        } elseif (count($parts) === 3) {
            return isset($return[$parts[0]][$parts[1]][$parts[2]]) ? $return[$parts[0]][$parts[1]][$parts[2]] : $default;
        }
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

    public function set($key, $value)
    {
        $parts = explode('/', $key);
        $file = array_shift($parts);
        if (!$file) {
            return false;
        }

        $app = $this->registry->settings->get('currentApp');
        $environment = $this->registry->settings->get('currentEnvironment');

        if (count($parts) === 0) {
            $this->data[$file] = (array) $value;
            $this->save((array) $value, $file, $app, $environment);
            return true;
        } else {
            $k = implode('/', $parts);
            $this->toSave[$file][$k] = $value;
            return true;
        }

    }
    
    public function __destruct()
    {
        $app = $this->registry->settings->get('currentApp');
        $environment = $this->registry->settings->get('currentEnvironment');
        
        foreach ($this->toSave as $file => $keys) {
            $this->load($file, $app, $environment);
            foreach ($keys as $k => $v) {
                $parts = explode('/', $k);
                if (count($parts) === 1) {
                    $this->data[$file][$parts[0]] = $v;
                } elseif (count($parts) === 2) {
                    $this->data[$file][$parts[0]][$parts[1]] = $v;
                } elseif (count($parts) === 3) {
                    $this->data[$file][$parts[0]][$parts[1]][$parts[2]] = $v;
                } else {
                    $pointer = &$this->data[$file];
                    while (null !== ($index = array_shift($parts))) {
                        if (!isset($pointer[$index])) {
                            $pointer[$index] = array();

                        }
                        $pointer = &$pointer[$index];
                        if (count($parts) === 0) {
                            $pointer = $v;
                        }
                    }
                }
            }
            $this->save($this->data[$file], $file, $app, $environment);
        }
        return true;
    }

    public function exists($key)
    {
        $parts = explode('/', $key);
        $file = array_shift($parts);
        if (!$file) {
            return false;
        }
        $data = $this->get($key, null);
        if ($data === null || (count($parts) === 0 && !count($data))) {
            return false;
        }
        return true;
    }

    public function delete($key)
    {
        $parts = explode('/', $key);
        $file = array_shift($parts);
        if (!$file) {
            return $default;
        }

        $app = $this->registry->settings->get('currentApp');
        $environment = $this->registry->settings->get('currentEnvironment');

        if (count($parts) === 0) {
            $this->data[$file] = array();
            $this->save(array(), $file, $app, $environment);
            return true;
        } else {
            $this->load($file, $app, $environment);
            if (count($parts) === 1) {
                unset($this->data[$file][$parts[0]]);
                $this->save($this->data[$file], $file, $app, $environment);
                return true;
            } elseif (count($parts) === 2) {
                unset($this->data[$file][$parts[0]][$parts[1]]);
                $this->save($this->data[$file], $file, $app, $environment);
                return true;
            } elseif (count($parts) === 3) {
                unset($this->data[$file][$parts[0]][$parts[1]][$parts[2]]);
                $this->save($this->data[$file], $file, $app, $environment);
                return true;
            }
        }

        $pointer = &$this->data[$file];
        while (null !== ($index = array_shift($parts))) {
           if (isset($pointer[$index])) {
                $pointer = &$pointer[$index];
                if (count($parts) === 0) {
                    $pointer = null;
                }
            } else {
                break;
            }
        }

        $this->save($this->data[$file], $file, $app, $environment);
        return true;
    }

    public function clear($all = true)
    {
        if ($all) {
            $app = '[a-zA-Z0-9]+';
            $environment = '[a-zA-Z0-9]+';
        } else {
            $app = $this->registry->settings->get('currentApp');
            $environment = $this->registry->settings->get('currentEnvironment');
        }

        foreach (scandir($this->folder) as $file) {
            if (!is_file(CACHEPATH.$file)) {
                continue;
            }
            if (preg_match('#'.preg_quote($this->prefix, '#').'_[a-zA-Z0-9]+_'.$app.'_'.$environment.'\.php#', $file)) {
                unlink(CACHEPATH.$file);
            }
        }

        $this->data = array();


        return true;
    }

    protected function load($file, $app, $environment)
    {
        $fileKey = $file; //md5($file);
        if (!file_exists(CACHEPATH.$this->prefix.'_cache_'.$fileKey.'_'.$app.'_'.$environment.'.php')) {
            file_put_contents(CACHEPATH.$this->prefix.'_cache_'.$fileKey.'_'.$app.'_'.$environment.'.tmp.php', '<?php $data = array();');
            rename(CACHEPATH.$this->prefix.'_cache_'.$fileKey.'_'.$app.'_'.$environment.'.tmp.php', CACHEPATH.$this->prefix.'_cache_'.$fileKey.'_'.$app.'_'.$environment.'.php');
            $this->data[$file] = array();
        } else {
            $data = array();
            include CACHEPATH.$this->prefix.'_cache_'.$fileKey.'_'.$app.'_'.$environment.'.php';
            $this->data[$file] = $data;
        }
    }

    protected function save($data, $file, $app, $environment)
    {
        $fileKey = $file; //md5($file);
        file_put_contents(CACHEPATH.$this->prefix.'_cache_'.$fileKey.'_'.$app.'_'.$environment.'.tmp.php', '<?php ' . "\n" . $this->varExport($data, '$data', 2));
        rename(CACHEPATH.$this->prefix.'_cache_'.$fileKey.'_'.$app.'_'.$environment.'.tmp.php', CACHEPATH.$this->prefix.'_cache_'.$fileKey.'_'.$app.'_'.$environment.'.php');
        $this->data[$file] = $data;
    }

    public function varExport($data, $varname, $maxDepth = 2, $depth = 0)
    {
        if (is_array($data) && $depth < $maxDepth) {
            $output = '';
            if ($depth == 0) {
                $output .= $varname . ' = array();' . "\n";
                $output .= "\n";
            } elseif (!count($data)) {
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

