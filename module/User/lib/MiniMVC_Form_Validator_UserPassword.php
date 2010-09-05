<?php

class MiniMVC_Form_Validator_UserPassword extends MiniMVC_Form_Validator
{

    public function validate($element, $value)
    {
        $model = $element->getForm()->getModel();
        if (method_exists((object)$model, 'checkPassword')) {
            return $model->checkPassword($value);
        }
        return false;
    }

}

