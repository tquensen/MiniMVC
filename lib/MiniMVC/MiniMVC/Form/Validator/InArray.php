<?php
class MiniMVC_Form_Validator_InArray extends MiniMVC_Form_Validator
{

	public function validate($value)
	{
		if (!isset($this->options['array']) || !is_array($this->options['array']))
		{
			return false;
		}
        if (!empty($this->options['multiple'])) {
            if (!is_array($value)) {
                return false;
            }

            foreach ($value as $singleValue) {
                if (!in_array($singleValue, $this->options['array'], empty($singleValue) && $singleValue !== '0')) {
                    return false;
                }
            }
            return true;
        }
		return in_array($value, $this->options['array'], empty($value) && $value !== '0');
	}
}