<?php

/**
 * MiniMVC_Cache is used for caching
 */
class MiniMVC_DummyCache extends MiniMVC_Cache
{
    public function get($key, $default = null, $app = null, $environment = null)
    {
        return $default;
    }

    public function set($key, $value, $merge = false, $app = null, $environment = null)
    {
        return false;
    }
    
    public function exists($key, $app = null, $environment = null)
    {
        return false;
    }

    public function delete($key, $app = null, $environment = null)
    {
        return false;
    }

    public function clear($all = true, $app = null, $environment = null)
    {
        return true;
    }
}

