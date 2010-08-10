<?php

class MiniMVC_Form_Validator_UserPassword extends MiniMVC_Form_Validator
{

    public function validate($element, $value)
    {
        $record = &$element->getForm()->getRecord();
        if (method_exists((object)$record, 'checkPassword')) {
            return $record->checkPassword($value);
        }
        return false;
    }

}

