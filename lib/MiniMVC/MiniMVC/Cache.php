<?php

/**
 * MiniMVC_Cache is used for caching
 */
class MiniMVC_Cache
{
    /**
     *
     * @var MiniMVC_Registry
     */
    protected $registry = null;
    protected $prefix = null;
    protected $folder = null;

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();
        $this->prefix = $this->registry->settings->get('config/cachePrefix', 'minimvc_');
        $this->folder = $this->registry->settings->get('config/cacheFolder', CACHEPATH);
    }

    abstract public function get($key, $default = null, $app = null, $environment = null);

    abstract public function set($key, $value, $merge = false, $app = null, $environment = null);
    
    abstract public function exists($key, $app = null, $environment = null);
}

