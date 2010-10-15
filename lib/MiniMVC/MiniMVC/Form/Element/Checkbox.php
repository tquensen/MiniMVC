<?php
class MiniMVC_Form_Element_Checkbox extends MiniMVC_Form_Element
{
	protected $type = 'checkbox';

    public function setValue($value)
	{
        parent::setValue((bool) $value);
    }

}