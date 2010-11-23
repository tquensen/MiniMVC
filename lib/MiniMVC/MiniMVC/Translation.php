<?php
/**
 * MiniMVC_Translation holds an array of translated data
 */
class MiniMVC_Translation implements ArrayAccess
{
	protected $translations = array();

	public function __construct($data = array())
	{
		foreach ($data as $key => $value)
		{
			$this->translations[$key] = $value;
		}
	}

	public function __set($key, $value)
	{
		$this->translations[$key] = $value;
	}

	public function __get($key)
	{
        $realkey = is_array($key) ? end($key) : $key;
        
		return (isset($this->translations[$realkey])) ? $this->translations[$realkey] : $key;
	}

    public function __isset($key)
	{
        $realkey = is_array($key) ? end($key) : $key;
        return isset($this->translations[$realkey]);
	}

    public function __unset($key)
	{
        if (isset($this->translations[$key])) {
            unset($this->translations[$key]);
        }
	}

    public function offsetSet($offset, $data)
    {
        if ($offset === null) {
            return;
        }
        $this->set($offset, $value);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetExists($offset)
    {
        return $this->_isset($offset);
    }

    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

	public function __call($key, $args)
	{
		return $this->get($key, (isset($args[0])) ? $args[0] : null, (isset($args[1])) ? $args[1] : null);
	}

    /**
     *
     * @param string $key the key/name of the translation
     * @param string|array $params parameter values, either as query string "foo=foovalue&bar=baz" or as array('foo'=>'foovalue','bar'=>'baz')
     * @param int|null $subkey if the translation is an array, this specifies which array key to use. when passing null or an invalid key, the last element will be used.
     * @return string the translated string
     */
	public function get($key, $params = null, $subkey = null)
	{
		$string = $this->__get($key);
		if (is_array($string))
		{
			if ($subkey !== null && isset($string[$subkey]))
			{
				$string = $string[$subkey];
			}
			else
			{
				$string = array_pop($string);
			}
		}
		if ($params !== null)
		{
			if (is_string($params))
			{
				$params = $this->_parseQueryString($params);
			}
			$search = array();
			$replace = array();
			foreach ((array) $params as $key=>$value)
			{
				$search[] = '{'.$key.'}';
				$replace[] = $value;
			}
			$string = str_replace($search, $replace, $string);
		}
		return $string;
	}

	public function _parseQueryString($string)
	{
		$return = array();
		foreach (explode('&', $string) as $param)
		{
			$param = explode('=', $param, 2);
			if (isset($param[1]))
			{
				$return[$param[0]] = $param[1];
			}
			else
			{
				$return['param'] = $param[0];
			}
		}
		return $return;
	}
}