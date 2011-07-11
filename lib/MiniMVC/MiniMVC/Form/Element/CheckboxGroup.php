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
            $values = array();
            foreach ($this->options['elements'] as $key => $value) {
                if (is_array($value)) {
                    foreach($value as $k => $v) {
                        $values[] = $k;
                    }
                } else {
                    $values[] = $key;
                }
            }
			$this->validators[] = new MiniMVC_Form_Validator_InArray(array('errorMessage' => $this->errorMessage, 'array' => $values, 'multiple' => true));
		}

		return parent::validate();
	}

    public function toArray($public = true)
    {
        $element = parent::toArray($public);
        $element['fullName'] = $element['fullName'] . '[]';
        if ($public) {
            $element['elements'] = $this->options['elements'];
        }
        return $element;
    }
}