<?php
class MiniMVC_Form_Validator_Required extends MiniMVC_Form_Validator
{
	public function validate($value)
	{
		return (bool) $value;
	}
}