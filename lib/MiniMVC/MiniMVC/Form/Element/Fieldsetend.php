<?php
class MiniMVC_Form_Element_Fieldsetend extends MiniMVC_Form_Element
{
	protected $type = 'fieldsetend';

    public function setValue($value)
	{
	}

	public function updateModel($model)
	{
	}

    public function toArray($public = true)
    {
        $element = parent::toArray($public);
        if ($public) {
            return false;
        }
        return $element;
    }
}