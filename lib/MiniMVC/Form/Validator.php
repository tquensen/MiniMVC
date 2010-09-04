<?php
class MiniMVC_Form_Validator
{
	protected $options = array();

	public function __construct($options = array())
	{
		$this->options = $options;
	}

	public function getOption($option)
	{
		return (isset($this->options[$option])) ? $this->options[$option] : false;
	}

	public function setOption($option, $value)
	{
		$this->options[$option] = $value;
	}

	public function __get($option)
	{
		return $this->getOption($option);
	}

	public function __set($option, $value)
	{
		$this->setOption($option, $value);
	}

	public function validate($element, $value)
	{
		return true;
	}
}