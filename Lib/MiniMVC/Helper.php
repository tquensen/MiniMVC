<?php
/**
 * MiniMVC_Helper is the base class for all helper classes
 */
class MiniMVC_Helper
{
    /**
     *
     * @var MiniMVC_Registry
     */
    protected $registry = null;
    protected $module = null;

    /**
     *
     * @param string $module the name of a module associated with this helper
     */
    public function __construct($module = null)
	{
        $this->module = $module;
		$this->registry = MiniMVC_Registry::getInstance();
	}
}