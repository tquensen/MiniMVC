<?php
/**
 * MiniMVC_Helpers is the container for individual helper classes
 *
 * @property Helper_Css $css
 * @property Helper_Js $js
 * @property Helper_Navi $navi
 * @property Helper_I18n $i18n
 * @property Helper_L10n $l10n
 * @property Helper_Partial $partial
 * @property Helper_Static $static
 * @property Helper_Url $url
 * @property Helper_Pager $pager
 * @property Helper_Text $text
 * @property Helper_Meta $meta
 * @property Helper_Messages $messages
 * @property Helper_Cache $cache
 *
 * @method Helper_Css css()
 * @method Helper_Js js()
 * @method Helper_Navi navi()
 * @method Helper_I18n i18n()
 * @method Helper_L10n l10n()
 * @method Helper_Partial partial()
 * @method Helper_Static static()
 * @method Helper_Url url()
 * @method Helper_Pager pager()
 * @method Helper_Text text()
 * @method Helper_Meta meta()
 * @method Helper_Messages messages()
 * @method Helper_Cache cache()
 * 
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
        $name = ucfirst($name);
        $module = (isset($arguments[0])) ? $arguments[0] . '_' : '';
        if (!isset($this->helpers[$module.$name]))
		{
			$helperName = $module.'Helper_'.$name;
			if (class_exists($helperName))
			{
				$this->helpers[$module.$name] = new $helperName($module);
			} else {
                $helperName = 'Helper_'.$name;
                if (class_exists($helperName))
                {
                    $this->helpers[$module.$name] = new $helperName($module);
                }
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
        $name = ucfirst($name);
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