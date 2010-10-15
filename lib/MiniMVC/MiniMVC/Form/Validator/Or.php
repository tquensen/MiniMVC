<?php
class MiniMVC_Form_Validator_Or extends MiniMVC_Form_Validator
{
    protected $validators = array();

    public function __construct($validators = array(), $options = array())
	{
        $this->validators = $validators;
		$this->options = $options;

        foreach ($this->validators as $validator) {
            $validator->setElement($this->getElement());
        }
	}

	public function validate($value)
	{

        foreach ($this->validators as $validator)
        {
            if ($validator->validate($value)) {
                return true;
            }
        }
		return false;
	}
}