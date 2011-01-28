<?php

/**
 * MiniMVC_Cache is used for caching
 */
class MiniMVC_ApcCache extends MiniMVC_Cache
{
    protected $data = array();

    public function get($key, $default = null, $app = null, $environment = null)
    {
        $parts = explode('/', $key);
        $file = array_shift($parts);
        if (!$file) {
            return $default;
        }

        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if (!isset($this->data[$file . '_' . $app . '_' . $environment])) {
            $this->load($file, $app, $environment);
        }

        $return = $this->data[$file . '_' . $app . '_' . $environment];
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

    public function set($key, $value, $app = null, $environment = null)
    {
        $parts = explode('/', $key);
        $file = array_shift($parts);
        if (!$file) {
            return false;
        }

        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if (count($parts) === 0) {
            $this->data[$file . '_' . $app . '_' . $environment] = (array) $value;
            $this->save((array) $value, $file, $app, $environment);
            return true;
        }

        $this->load($file, $app, $environment);

        $pointer = &$this->data[$file . '_' . $app . '_' . $environment];
        while (null !== ($index = array_shift($parts))) {
            if (!isset($pointer[$index])) {
                $pointer[$index] = array();

            }
            $pointer = &$pointer[$index];
            if (count($parts) === 0) {
                $pointer = $value;
            }
        }

        $this->save($this->data[$file . '_' . $app . '_' . $environment], $file, $app, $environment);
        return true;
    }

    public function exists($key, $app = null, $environment = null)
    {
        $parts = explode('/', $key);
        $file = array_shift($parts);
        if (!$file) {
            return false;
        }
        $data = $this->get($key, null, $app, $environment);
        if ($data === null || (count($parts) === 0 && !count($data))) {
            return false;
        }
        return true;
    }

    public function delete($key, $app = null, $environment = null)
    {
        $parts = explode('/', $key);
        $file = array_shift($parts);
        if (!$file) {
            return $default;
        }

        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if (count($parts) === 0) {
            $this->data[$file . '_' . $app . '_' . $environment] = array();
            $this->save(array(), $file, $app, $environment);
            return true;
        }

        $this->load($file, $app, $environment);

        $pointer = &$this->data[$file . '_' . $app . '_' . $environment];
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

        $this->save($this->data[$file . '_' . $app . '_' . $environment], $file, $app, $environment);
        return true;
    }

    public function clear($all = true, $app = null, $environment = null)
    {
        if (!$info = apc_cache_info('user')) {
            return false;
        }

        if ($all) {
            $app = '[a-zA-Z0-9]+';
            $environment = '[a-zA-Z0-9]+';

            $this->data = array();
        } else {
            $app = ($app) ? $app : $this->registry->settings->get('currentApp');
            $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');
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

        foreach ($this->data as $key => $value) {
            if (preg_match('#.+_'.$app.'_'.$environment.'#', $key)) {
                unset($this->data[$key]);
            }
        }

        return true;
    }

    protected function load($file, $app, $environment)
    {
        $fileKey = md5($file);
        $success = null;
        $data = apc_fetch($this->prefix.'_'.$fileKey.'_'.$app.'_'.$environment, $success);
        if ($success) {
            $this->data[$file.'_'.$app.'_'.$environment] = $data;
            return true;
        } else {
            $this->data[$file.'_'.$app.'_'.$environment] = array();
            return false;
        }
    }

    protected function save($data, $file, $app, $environment)
    {
        $fileKey = md5($file);
        $this->data[$file.'_'.$app.'_'.$environment] = $data;
        return apc_store($this->prefix.'_'.$fileKey.'_'.$app.'_'.$environment, $value);
    }
}

