<?php
class MiniMVC_Form_Validator_InArray extends MiniMVC_Form_Validator
{
	public function setArray($array)
	{
		$this->array = $array;
	}

	public function validate($element, $value)
	{
		if (!isset($this->options['array']) || !is_array($this->options['array']))
		{
			return false;
		}
		return in_array($value, $this->options['array'], true);
	}
}