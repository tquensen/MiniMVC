<?php

/**
 * MiniMVC_Cache is used for caching
 */
class MiniMVC_ApcCache extends MiniMVC_Cache
{
    protected $data = array();

    public function get($key, $default = null, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if (isset($this->data[$app.'_'.$environment][$key])) {
            return $this->data[$app.'_'.$environment][$key];
        } elseif(isset($this->data[$app.'_'.$environment]) && array_key_exists($key, $this->data[$app.'_'.$environment])) {
            return $default;
        }

        $success = null;
        $data = apc_fetch($this->prefix.'_'.$app.'_'.$environment.'_'.$key, $success);
        if ($success) {
            $this->data[$app.'_'.$environment][$key] = $data;
            return $data;
        } else {
            $this->data[$app.'_'.$environment][$key] = null;
            return $default;
        }
    }

    public function set($key, $value, $merge = false, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if ($merge) {
            $success = null;
            $data = apc_fetch($this->prefix.'_'.$app.'_'.$environment.'_'.$key, $success);
            if ($success) {
                $value = array_merge((array) $data, (array) $value);
            }
        }
        $this->data[$app.'_'.$environment][$key] = $value;
        return apc_store($this->prefix.'_'.$app.'_'.$environment.'_'.$key, $value);
    }
    
    public function exists($key, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if (isset($this->data[$app.'_'.$environment][$key])) {
            return true;
        } elseif(isset($this->data[$app.'_'.$environment]) && array_key_exists($key, $this->data[$app.'_'.$environment])) {
            return false;
        }
        return apc_exists($this->prefix.'_'.$app.'_'.$environment.'_'.$key);
    }

    public function delete($key, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');
        
        unset($this->data[$app.'_'.$environment][$key]);

        return apc_delete($this->prefix.'_'.$app.'_'.$environment.'_'.$key);
    }

    public function clear($all = true, $app = null, $environment = null)
    {
        if (!$info = apc_cache_info('user')) {
            return false;
        }

        if ($all) {
            $app = '[\w]+';
            $environment = '[\w]+';

            $this->data = array();
        } else {
            $app = ($app) ? $app : $this->registry->settings->get('currentApp');
            $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');
        
            unset($this->data[$app.'_'.$environment]);
        }

        foreach ($info['cache_list'] as $entry) {
            if ($info['type'] != 'user') {
                continue;
            }
            if (!preg_match('#'.preg_quote($this->prefix, '#').'_'.$app.'_'.$environment.'_.+#', $entry['info'])) {
                continue;
            }
            apc_delete($entry['info']);
        }

        return true;
    }
}

