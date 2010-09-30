<?php
class MiniMVC_Form_Validator_File extends MiniMVC_Form_Validator
{
	public function validate($element, $value)
	{
		if ($element->fileError && ($this->required || $element->fileError != UPLOAD_ERR_NO_FILE)) {
            return false;
        }

        if (file_exists($element->fileTempName)) {
            $element->value = $element->fileTempName;
        }
        return true;
	}
}