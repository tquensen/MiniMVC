<?php
class Helper_Cache extends MiniMVC_Helper {

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
        $cache = new Helper_Cache();
        $cache->init($conditions, $tokens, $bindToUrl);
        return $cache;
    }

    public function init($conditions = array(), $tokens = array(), $bindToUrl = true)
    {
        $conditions = (array) $conditions;
        if ($bindToUrl) {
            $this->urlHash = md5($this->registry->settings->get('currentUrlFull'));
        }

        ksort($conditions);

        $this->conditions = $conditions;
        $this->tokens = (array) $tokens;

        $conditionIdentifier = md5(serialize($conditions));

        $this->key = $conditionIdentifier;
    }

    public function setTTL($time)
    {
        $this->ttl = $time;
    }

    public function check()
    {
        if (!$this->key || !file_exists(CACHEPATH.'views/'.$this->urlHash.'/'.$this->key.'.php')) {
            return false;
        }
        $expires = $this->registry->cache->get('viewcache_ttl/'.$this->urlHash.'_'.$this->key);
        if (!is_int($expires) || $expires < time()) {
            unlink(CACHEPATH.'views/'.$this->urlHash.'/'.$this->key.'.php');
            return false;
        }
        return true;
    }

    public function load()
    {
        $expires = $this->registry->cache->get('viewcache_ttl/'.$this->urlHash.'_'.$this->key);
        if (file_exists(CACHEPATH.'views/'.$this->urlHash.'/'.$this->key.'.php')) {
            if (!is_int($expires) || $expires < time()) {
                unlink(CACHEPATH.'views/'.$this->urlHash.'/'.$this->key.'.php');
                return '';
            }
            ob_start();
            include CACHEPATH.'views/'.$this->urlHash.'/'.$this->key.'.php';
            return ob_get_clean();
        }

        return '';
    }

    public function save($content)
    {
        if (!$this->key) {
            return false;
        }

        $tokens = array();
        foreach ((array) $this->tokens as $token) {
            $this->registry->cache->set('viewcache_token_'.$token.'/'.$this->urlHash.'_'.$this->key, true);
        }

        if (!is_dir(CACHEPATH.'views')) {
            mkdir(CACHEPATH.'views');
        }
        if (!is_dir(CACHEPATH.'views/'.$this->urlHash)) {
            mkdir(CACHEPATH.'views/'.$this->urlHash);
        }

        $this->registry->cache->set('viewcache_ttl/'.$this->urlHash.'_'.$this->key, $this->ttl === false ? 0 : time() + $this->ttl);
        file_put_contents(CACHEPATH.'views/'.$this->urlHash.'/'.$this->key.'.php', $content);
        return true;
    }

    public function delete($tokens = array())
    {
        foreach ((array)$tokens as $token) {
            $cache = $this->registry->cache->get('viewcache_token_'.$token, array());
            if ($cache) {
                foreach ($cache as $path => $dummy) {
                    $path = str_replace('_','/',$path);
                    if (file_exists(CACHEPATH.'views/'.$path.'.php')) {
                        unlink(CACHEPATH.'views/'.$path.'.php');
                    }
                }
                $this->registry->cache->delete('viewcache_token_'.$token);
            }         
        }
        
        return true;
    }
}
?>
