<?php
class MiniMVC_Form_Element_Password extends MiniMVC_Form_Element
{
	protected $type = 'password';

    public function toArray($public = true)
    {
        $element = parent::toArray($public);
        if ($public) {
            $element['value'] = '';
        }
        return $element;
    }
}