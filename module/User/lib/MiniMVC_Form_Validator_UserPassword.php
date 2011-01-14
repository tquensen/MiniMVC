<?php

class MiniMVC_Form_Validator_UserPassword extends MiniMVC_Form_Validator
{

    public function validate($value)
    {
        $model = $this->getForm()->getModel();
        if (!($model instanceof User)) {
            return false;
        }
        if ($model->isNew()) {
            if (!$this->loginElement) {
                return false;
            }
            $loginBy = $this->loginElement->modelProperty ? $this->loginElement->modelProperty : $this->loginElement->getName();
            $loginValue = $this->loginElement->value;
            $realModel = $model->getTable()->loadOneBy($loginBy . ' = ?', array($loginValue));
            if (!$realModel || !$realModel->checkPassword($value)) {
                return false;
            }
            $this->getForm()->setModel($model);
            return true;
        }

        return $model->checkPassword($value);
    }

}

