<?php 
class MiniMVC_Helpers
{
	protected $helpers = array();
	protected $registry = null;
	
	public function __construct()
	{
		$this->registry = MiniMVC_Registry::getInstance();
	}

    public function __call($name, $arguments)
    {
        $module = (isset($arguments[0])) ? $arguments[0] : null;
        if (!isset($this->helpers[$module.'/'.$name]))
		{
			$helperName = 'Helper_'.$name;
			if (class_exists($helperName))
			{
				$this->helpers[$module.'/'.$name] = new $helperName($module);
			}
		}
		return (isset($this->helpers[$module.'/'.$name])) ? $this->helpers[$module.'/'.$name] : null;
    }

	public function __get($name)
	{                
		if (!isset($this->helpers[$name]))
		{
			$helperName = 'Helper_'.$name;
			if (class_exists($helperName))
			{
				$this->helpers[$name] = new $helperName();
			}
		}
		return (isset($this->helpers[$name])) ? $this->helpers[$name] : null;
	}
}