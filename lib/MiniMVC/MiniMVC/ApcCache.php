<?php

/**
 * MiniMVC_Cache is used for caching
 */
class MiniMVC_ApcCache extends MiniMVC_Cache
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
        $data = $this->get($key);
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
        if (!$info = apc_cache_info('user')) {
            return false;
        }

        if ($all) {
            $app = '[a-zA-Z0-9]+';
            $environment = '[a-zA-Z0-9]+';       
        } else {
            $app = $this->registry->settings->get('currentApp');
            $environment = $this->registry->settings->get('currentEnvironment');
        }

        foreach ($info['cache_list'] as $entry) {
            if ($info['type'] != 'user') {
                continue;
            }
            if (!preg_match('#'.preg_quote($this->prefix, '#').'_[a-zA-Z0-9]+_'.$app.'_'.$environment.'#', $entry['info'])) {
                continue;
            }
            apc_delete($entry['info']);
        }

        $this->data = array();

        return true;
    }

    protected function load($file, $app, $environment)
    {
        $fileKey = md5($file);
        $success = null;
        $data = apc_fetch($this->prefix.'_'.$fileKey.'_'.$app.'_'.$environment, $success);
        if ($success && $data) {
            $this->data[$file] = $data;
            return true;
        } else {
            $this->data[$file] = array();
            return false;
        }
    }

    protected function save($data, $file, $app, $environment)
    {
        $fileKey = md5($file);
        $this->data[$file] = $data ? $data : array();
        if (!$data) {
            return apc_delete($this->prefix.'_'.$fileKey.'_'.$app.'_'.$environment);
        }
        return apc_store($this->prefix.'_'.$fileKey.'_'.$app.'_'.$environment, $data);
    }
}

