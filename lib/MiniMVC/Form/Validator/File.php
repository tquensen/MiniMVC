<?php
class MiniMVC_Form_Validator_File extends MiniMVC_Form_Validator
{
	public function validate($element, $value)
	{
        if (!is_array($value)) {
            return false;
        }

		if ($value['error'] && ($this->required || $value['error'] != UPLOAD_ERR_NO_FILE)) {
            return false;
        }

        if (!file_exists($value['tmp_name'])) {
            return false;
        }
        
        return true;
	}
}