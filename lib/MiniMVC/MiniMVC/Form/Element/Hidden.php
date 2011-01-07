<?php
class MiniMVC_Form_Element_Hidden extends MiniMVC_Form_Element
{
	protected $type = 'hidden';

    public function toArray($public = true)
    {
        $element = parent::toArray($public);
        if ($public && $this->alwaysDisplayDefault) {
            $element['value'] = $element['options']['defaultValue'];
        }
        return $element;
    }
}