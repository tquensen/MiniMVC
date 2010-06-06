<?php
class MiniMVC_Guard {

	protected $registry = null;
	protected $id = null;
	protected $role = 'guest';
	protected $rights = 0;
	protected $data = array();

	public function __construct()
	{
		$this->registry = MiniMVC_Registry::getInstance();
		
		if (isset($_SESSION['guardID']))
		{
			$this->id = $_SESSION['guardID'];
		}
		if (isset($_SESSION['guardRole']) && $_SESSION['guardRole'])
		{
			$this->role = $_SESSION['guardRole'];
		}
		if (isset($_SESSION['guardData']))
		{
			$this->data = $_SESSION['guardData'];
		}
		if (isset($this->registry->settings->roles[$this->role]['rights']))
		{
			$this->rights = (int) $this->registry->settings->roles[$this->role]['rights'];
		}
		
	}

	public function setUser($id = null, $role = 'guest')
	{
		$this->setId($id);
		$this->setRole($role);
		$this->clearData();
	}

	public function setId($id)
	{
		$this->id = $id;
		$_SESSION['guardID'] = $id;
	}

	public function setRole($role)
	{
		$this->role = $role;
		$_SESSION['guardRole'] = $role;
		$this->rights = (isset($this->registry->settings->roles[$this->role]['rights'])) ? (int) $this->registry->settings->roles[$this->role]['rights'] : 0;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getRole()
	{
		return $this->role;
	}

	public function getRights()
	{
		return $this->rights;
	}
	
	public function clearData()
	{
		$this->data = array();
		$_SESSION['guardData'] = array();
	}
	
	public function __get($key)
	{
		return (isset($this->data[$key])) ? $this->data[$key] : null;	
	}
	
	public function __set($key, $value = null)
	{
		if (is_array($key))
		{
			foreach ($key as $k => $v)
			{
				$this->setData($k, $v);
			}
            return true;
		}
		$this->data[$key] = $value;
		$_SESSION['guardData'] = $this->data;
		return true;
	}

    

}