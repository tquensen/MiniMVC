<?php
class MiniMVC_Form_Element_File extends MiniMVC_Form_Element
{
	protected $type = 'file';

    public function setValue($value)
	{
        parent::setValue($value);

        if (isset($_FILES[$this->getForm()->getName()])) {
            if (isset($_FILES[$this->getForm()->getName()]['name'][$this->name])) {
                $this->fileName = $_FILES[$this->getForm()->getName()]['name'][$this->name];
            }
            if (isset($_FILES[$this->getForm()->getName()]['tmp_name'][$this->name])) {
                $this->fileTempName = $_FILES[$this->getForm()->getName()]['tmp_name'][$this->name];
            }
            if (isset($_FILES[$this->getForm()->getName()]['type'][$this->name])) {
                $this->fileType = $_FILES[$this->getForm()->getName()]['type'][$this->name];
            }
            if (isset($_FILES[$this->getForm()->getName()]['size'][$this->name])) {
                $this->fileSize = $_FILES[$this->getForm()->getName()]['size'][$this->name];
            }
            if (isset($_FILES[$this->getForm()->getName()]['error'][$this->name])) {
                $this->fileError = $_FILES[$this->getForm()->getName()]['error'][$this->name];
            }
        }
	}
}