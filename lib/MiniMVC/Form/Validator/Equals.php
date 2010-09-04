<?php
class MiniMVC_Form_Validator_Equals extends MiniMVC_Form_Validator
{
	public function validate($element, $value)
	{
        $checkValue = $this->getOption('value');
        if (is_object($checkValue) && $checkValue instanceof MiniMVC_Form_Element)
        {
            $checkValue = $checkValue->value;
        }

		return (bool) ($this->getOption('strict')) ? $value === $checkValue : $value == $checkValue;
	}
}