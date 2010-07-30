<?php
class MiniMVC_Form_Element_CheckboxGroup extends MiniMVC_Form_Element
{
	protected $type = 'checkboxGroup';

    public function __construct($name = false, $options = array(), $validators = array())
	{
		parent::__construct($name, $options, $validators);
		if (!isset($this->options['elements']))
		{
			$this->options['elements'] = array();
		}
	}

    public function validate()
	{
		if ($this->value !== null && !$this->getOption('skipDefaultValidator'))
		{
			$this->validators[] = new MiniMVC_Form_Validator_InArray(array('array' => array_keys($this->options['elements'])));
		}

		return parent::validate();
	}
}