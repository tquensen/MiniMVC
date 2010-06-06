<?php
class MiniMVC_Helper
{
    protected $registry = null;
    protected $module = null;

    public function __construct($module = null)
	{
        $this->module = $module;
		$this->registry = MiniMVC_Registry::getInstance();
	}
}