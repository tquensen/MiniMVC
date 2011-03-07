<?php

/**
 * MiniMVC_Cache is used for caching
 */
class MiniMVC_DummyCache extends MiniMVC_Cache
{
    public function get($key, $default = null)
    {
        return $default;
    }

    public function set($key, $value)
    {
        return false;
    }
    
    public function exists($key)
    {
        return false;
    }

    public function delete($key)
    {
        return false;
    }

    public function clear($all = true)
    {
        return true;
    }
}

