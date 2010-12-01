<?php

/**
 * MiniMVC_Cache is used for caching
 */
class MiniMVC_ApcCache extends MiniMVC_Cache
{
    public function get($key, $default = null, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        $data = apc_fetch($this->prefix.'_'.$app.'_'.$environment.'_'.$key, $success = null);
        return $success ? $data : $default;
    }

    public function set($key, $value, $merge = false, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');

        if ($merge) {
            $data = apc_fetch($this->prefix.'_'.$app.'_'.$environment.'_'.$key, $success = null);
            if ($success) {
                $value = array_merge((array) $data, (array) $value);
            }
        }
        return apc_store($this->prefix.'_'.$app.'_'.$environment.'_'.$key, $value);
    }
    
    public function exists($key, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');
        return apc_exists($this->prefix.'_'.$app.'_'.$environment.'_'.$key);
    }

    public function delete($key, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');
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
        } else {
            $app = ($app) ? $app : $this->registry->settings->get('currentApp');
            $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');
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

