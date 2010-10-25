<?php
class MiniMVC_Form_Element_File extends MiniMVC_Form_Element
{
	protected $type = 'file';

    public function setValue($value)
	{
        $this->value = null;
        
        if (isset($_FILES[$this->getForm()->getName()])) {
            $value = array();

            if (isset($_FILES[$this->getForm()->getName()]['name'][$this->name])) {
                $value['name'] = $_FILES[$this->getForm()->getName()]['name'][$this->name];
            } else {
                $value['name'] = null;
            }
            if (isset($_FILES[$this->getForm()->getName()]['tmp_name'][$this->name])) {
                $value['tmp_name'] = $_FILES[$this->getForm()->getName()]['tmp_name'][$this->name];
            } else {
                $value['tmp_name'] = null;
            }
            if (isset($_FILES[$this->getForm()->getName()]['type'][$this->name])) {
                $value['type'] = $_FILES[$this->getForm()->getName()]['type'][$this->name];
            } else {
                $value['type'] = null;
            }
            if (isset($_FILES[$this->getForm()->getName()]['error'][$this->name])) {
                $value['error'] = $_FILES[$this->getForm()->getName()]['error'][$this->name];
            } else {
                $value['error'] = null;
            }
            if (isset($_FILES[$this->getForm()->getName()]['name'][$this->name])) {
                $value['name'] = $_FILES[$this->getForm()->getName()]['name'][$this->name];
            } else {
                $value['name'] = null;
            }
            
            $this->value = !empty($value) ? $value : null;
        }
	}
}