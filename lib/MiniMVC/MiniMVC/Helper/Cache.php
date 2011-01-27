<?php
class Helper_Cache extends MiniMVC_Helper {

    /**
     *
     * @var MiniMVC_Registry
     */
	protected $registry = null;

    protected $name = false;
    protected $conditions = false;
    protected $key = false;
    protected $tokens = array();

    public function get($name, $conditions = array(), $tokens = array(), $bindToUrl = true)
    {
        $cache = new Helper_Cache();
        $cache->init($name, $conditions, $tokens, $bindToUrl);
        return $cache;
    }

    public function init($name, $conditions = array(), $tokens = array(), $bindToUrl = true)
    {
        $conditions = (array) $conditions;
        if ($bindToUrl) {
            $conditions['url'] = $this->registry->settings->get('currentUrlFull');
        }

        ksort($conditions);

        $this->name = $name;
        $this->conditions = $conditions;
        $this->tokens = (array) $tokens;

        $conditionIdentifier = md5(serialize($conditions));

        $this->key = md5($name.'.'.$conditionIdentifier);
    }

    public function check()
    {
        if (!$this->key || !file_exists(CACHEPATH.'cache_'.$this->key.'.php')) {
            return false;
        }
        return true;
    }

    public function load()
    {
        if (!$this->check()) {
            return false;
        }
        return file_get_contents(CACHEPATH.'cache_'.$this->key.'.php');
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
        $data = array(
            'conditions' => (array) $this->conditions,
            'tokens' => $tokens
        );
        if (!$this->registry->cache->set('viewContentCached/'.$this->key, $data)) {
            return false;
        }
        file_put_contents(CACHEPATH.'cache_'.$this->key.'.tmp.php', $content);
        rename(CACHEPATH.'cache_'.$this->key.'.tmp.php', CACHEPATH.'cache_'.$this->key.'.php');
    }

    public function delete($tokens = array())
    {
        $cache = $this->registry->cache->get('viewContentCached', array());
        foreach ($cache as $key => $data) {
            foreach ((array)$tokens as $token) {
                if (isset($data['tokens'][$token])) {
                    $this->registry->cache->delete('viewContentCached/'.$key);
                    if (file_exists(CACHEPATH.'cache_'.$key.'.php')) {
                        unlink(file_exists(CACHEPATH.'cache_'.$key.'.php'));
                    }
                    break;
                }
            }
        }
        return true;
    }
}
?>
