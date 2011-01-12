<?php
class MiniMVC_Form_Element_SelectMultiple extends MiniMVC_Form_Element
{
	protected $type = 'selectMultiple';

    public function __construct($name = false, $options = array(), $validators = array())
	{
		parent::__construct($name, $options, $validators);
		if (!isset($this->options['options']))
		{
			$this->options['options'] = array();
		}
	}

    public function setValue($value)
	{
        parent::setValue($value);
        if (!is_array($this->value)) {
            $this->value = (array) $this->value;
        }
	}

    public function validate()
	{
		if ($this->value && !$this->getOption('skipDefaultValidator'))
		{
			$this->validators[] = new MiniMVC_Form_Validator_InArray(array('array' => array_keys($this->options['options']), 'multiple' => true));
		}

		return parent::validate();
	}

    public function toArray($public = true)
    {
        $element = parent::toArray($public);
        $element['fullName'] = $element['fullName'] . '[]';
        if ($public) {
            $element['options']['options'] = $this->options['options'];
            if ($this->options['size']) {
                $element['options']['size'] = $this->options['size'];
            }
        }
        return $element;
    }
}