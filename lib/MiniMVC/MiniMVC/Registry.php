<?php
/**
 * MiniMVC_Registry is the global registry for some frequently used classes
 *
 * @property MiniMVC_Settings $settings stores the configuration arrays
 * @property MiniMVC_Cache $cache the caching class
 * @property MiniMVC_Dispatcher $dispatcher is responsible for any route calls
 * @property MiniMVC_Layout $layout the default layout/template class
 * @property MiniMVC_Guard $guard is responsible for the current user session
 * @property MiniMVC_Rights $rights is responsible for the role/right management
 * @property MiniMVC_Pdo $db is responsible for the current database connection
 * @property MiniMVC_Helpers $helper is the container for individual helper classes
 * @property MiniMVC_Task $task is used to dispatch and call CLI tasks
 * @property MiniMVC_Events $events the event dispatcher
 */
class MiniMVC_Registry
{
	protected $data = array();
	private static $instances = array();

    /**
     *
     * @param integer $x the index of the instance
     * @return MiniMVC_Registry
     */
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
		if ($className = $this->data['settings']->get('config/registryClasses/'.$key))
		{
			return $this->data[$key] = new $className();
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