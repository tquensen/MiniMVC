<?php
/**
 * MiniMVC_Helpers is the container for individual helper classes
 *
 * @property Helper_CSS $CSS
 * @property Helper_I18n $I18n
 * @property Helper_Partial $Partial
 * @property Helper_Static $Static
 * @property Helper_Url $Url
 *
 * @method Helper_CSS CSS($module) CSS($module)
 * @method Helper_I18n I18n()
 * @method Helper_Partial Partial()
 * @method Helper_Static Static()
 * @method Helper_Url Url()
 */
class MiniMVC_Helpers
{
	protected $helpers = array();
    /**
     *
     * @var MiniMVC_Registry
     */
	protected $registry = null;
	
	public function __construct()
	{
		$this->registry = MiniMVC_Registry::getInstance();
	}

    /**
     *
     * @param string $name the name of a helper class
     * @param array $arguments the first argument shoud be a module name
     * @return MiniMVC_Helper
     */
    public function __call($name, $arguments)
    {
        $module = (isset($arguments[0])) ? $arguments[0] . '/' : '';
        if (!isset($this->helpers[$module.$name]))
		{
			$helperName = 'Helper_'.$name;
			if (class_exists($helperName))
			{
				$this->helpers[$module.$name] = new $helperName($module);
			}
		}
		return (isset($this->helpers[$module.$name])) ? $this->helpers[$module.$name] : null;
    }

    /**
     *
     * @param string $name the name of a helper class
     * @return MiniMVC_Helper
     */
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