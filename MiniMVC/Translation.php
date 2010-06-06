<?php
class MiniMVC_Translation
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
		return (isset($this->translations[$key])) ? $this->translations[$key] : '';
	}

	public function __call($key, $args)
	{
		return $this->t($key, (isset($args[0])) ? $args[0] : null, (isset($args[1])) ? $args[1] : null);
	}

	public function t($key, $params = null, $subkey = null)
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
				$params = $this->parseQueryString($params);
			}
			$search = array();
			$replace = array();
			foreach ((array) $params as $key=>$value)
			{
				$search[] = '{'.$key.'}';
				$replace[] = $value;
			}
			return str_replace($search, $replace, $string);
		}
		return $string;
	}

	public function parseQueryString($string)
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