<?php
class Helper_DummyCache extends MiniMVC_Helper {

    /**
     *
     * @var MiniMVC_Registry
     */
	protected $registry = null;

    protected $conditions = false;
    protected $key = false;
    protected $urlHash = 'other';
    protected $tokens = array();
    protected $ttl = false;

    public function get($conditions = array(), $tokens = array(), $bindToUrl = true)
    {
        $cache = new Helper_DummyCache();
        return $cache;
    }

    public function init($conditions = array(), $tokens = array(), $bindToUrl = true)
    {
    }

    public function check()
    {
        return false;
    }
    
    public function setTTL($time)
    {
        $this->ttl = $time;
    }

    public function load()
    {
    }

    public function save($content)
    {
        return false;
    }

    public function delete($tokens = array())
    {
        return true;
    }
}
?>
