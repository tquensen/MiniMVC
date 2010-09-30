<?php
class MiniMVC_Form_Validator_File extends MiniMVC_Form_Validator
{
	public function validate($element, $value)
	{
		if ($element->fileError && ($this->required || $element->fileError == UPLOAD_ERR_NO_FILE)) {
            return false;
        }

        if ($this->path && file_exists($element->fileTempName)) {
            $name = $this->generateFileName();
            if (file_exists(rtrim($this->path, '/') . '/'. $name)) {
                unlink(rtrim($this->path, '/') . '/'. $name);
            }
            rename($element->fileTempName, rtrim($this->path, '/') . '/'. $name);
            $element->setValue($this->webPath ? rtrim($this->webPath, '/') . '/'. $name : $name);
        }
        return true;
	}

    protected function generateFileName()
    {
        return $this->filename ? ($this->exactFilename ? $this->filename : time() . '_' . $this->filename) : md5(time().rand(10000, 99999));
    }
}