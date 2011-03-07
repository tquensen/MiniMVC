<?php

/**
 * MiniMVC_Cache is used for caching
 */
abstract class MiniMVC_Cache
{
    /**
     *
     * @var MiniMVC_Registry
     */
    protected $registry = null;
    protected $prefix = 'minimvc';

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();
        //$this->prefix = $this->registry->settings->get('config/cachePrefix', 'minimvc_');
        //$this->folder = $this->registry->settings->get('config/cacheFolder', CACHEPATH);
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    abstract public function get($key, $default = null);

    abstract public function set($key, $value);
    
    abstract public function exists($key);

    abstract public function delete($key);

    abstract public function clear($all = true);
}

