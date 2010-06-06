<?php 
class MiniMVC_Registry
{
	protected $data = array();
	private static $instances = array();
 
	static public function getInstance($x=0)
	{
		if (!isset(self::$instances[$x])) {
			self::$instances[$x] = new self;
		}
		return self::$instances[$x];
	}

	public function __set($key, $value)
	{
		if ( !isset($this->data[$key]) )
		{
			$this->data[$key] = $value;
		}
		else
		{
			throw new Exception('The key `' . $key . '` could not be set because it already exists.');
		}
	}
 
	public function __get($key)
	{
		if (isset($this->data[$key]))
		{
			return $this->data[$key];
		}
		if (isset($this->data['settings']->config['registryClasses'][$key]))
		{
			return $this->data[$key] = new $this->data['settings']->config['registryClasses'][$key]();
		}
		return null;
	}
 
	public function getAll()
	{
		return $this->data;
	}
 
	public function remove($key)
	{
		if ( isset($this->data[$key]) )
		{
			unset($this->data[$key]);
			return true;
		}
		else
		{
			return false;
		}
	}
 
	public function removeAll()
	{
		$this->data = array();
	}
}