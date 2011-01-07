<?php
class MiniMVC_Form_Element_Button extends MiniMVC_Form_Element
{
	protected $type = 'button';

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
            $element['options']['type'] = $this->options['type'];
        }
        return $element;
    }
}