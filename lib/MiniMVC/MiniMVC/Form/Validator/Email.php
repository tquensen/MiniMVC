<?php
class MiniMVC_Form_Validator_Email extends MiniMVC_Form_Validator
{
	public function validate($value)
	{
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
	}
}