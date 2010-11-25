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
    protected $prefix = 'minimvc';
    protected $folder = CACHEPATH;

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

    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getFolder()
    {
        return $this->folder;
    }

    abstract public function get($key, $default = null, $app = null, $environment = null);

    abstract public function set($key, $value, $merge = false, $app = null, $environment = null);
    
    abstract public function exists($key, $app = null, $environment = null);
}

