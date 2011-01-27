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

    abstract public function get($key, $default = null, $app = null, $environment = null);

    abstract public function set($key, $value, $app = null, $environment = null);
    
    abstract public function exists($key, $app = null, $environment = null);

    abstract public function delete($key, $app = null, $environment = null);

    abstract public function clear($all = true, $app = null, $environment = null);
}

