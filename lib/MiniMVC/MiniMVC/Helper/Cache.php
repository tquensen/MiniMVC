<?php
class Helper_Cache extends MiniMVC_Helper {

    /**
     *
     * @var MiniMVC_Registry
     */
	protected $registry = null;

    protected $conditions = false;
    protected $key = false;
    protected $urlHash = 'default';
    protected $tokens = array();

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

        $this->key = md5($conditionIdentifier);
    }

    public function check()
    {
        if (!$this->key || !$this->registry->cache->exists('viewcache_'.$this->urlHash.'/'.$this->key)) {
            return false;
        }
        return true;
    }

    public function load()
    {
        return $this->registry->cache->get('viewcache_'.$this->urlHash.'/'.$this->key);
    }

    public function save($content)
    {
        if (!$this->key) {
            return false;
        }

        $tokens = array();
        foreach ((array) $this->tokens as $token) {
            $tokens[$token] = true;
        }

        if (!$this->registry->cache->set('viewcache_'.$this->urlHash.'/'.$this->key, $content)
         || !$this->registry->cache->set('viewcache_tokens/'.$this->urlHash.'_'.$this->key, $tokens)
        ) {
            return false;
        }
    }

    public function delete($tokens = array())
    {
        $cache = $this->registry->cache->get('viewcache_tokens', array());
        foreach ($cache as $key => $cacheTokens) {
            foreach ((array)$tokens as $token) {
                if (isset($cacheTokens[$token])) {
                    $this->registry->cache->delete('viewcache_tokens/'.$key);
                    $this->registry->cache->delete('viewcache_'.str_replace('_','/', $key));
                    break;
                }
            }
        }
        return true;
    }
}
?>
