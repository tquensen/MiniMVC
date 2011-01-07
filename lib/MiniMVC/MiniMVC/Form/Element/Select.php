<?php
class MiniMVC_Form_Element_Select extends MiniMVC_Form_Element
{
	protected $type = 'select';

	public function __construct($name = false, $options = array(), $validators = array())
	{
		parent::__construct($name, $options, $validators);
		if (!isset($this->options['options']))
		{
			$this->options['options'] = array();
		}
	}

	public function validate()
	{
		if (!$this->getOption('skipDefaultValidator'))
		{
			$this->validators[] = new MiniMVC_Form_Validator_InArray(array('errorMessage' => $this->errorMessage, 'array' => array_keys($this->options['options'])));
		}

		return parent::validate();
	}

    public function toArray($public = true)
    {
        $element = parent::toArray($public);
        if ($public) {
            $element['options']['options'] = $this->options['options'];
        }
        return $element;
    }
}